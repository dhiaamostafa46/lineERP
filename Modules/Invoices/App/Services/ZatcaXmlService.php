<?php

namespace Modules\Invoices\App\Services;

use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\ZatcaSetting;
use Salla\ZATCA\Helpers\UXML;

class ZatcaXmlService
{
    /**
     * Generate UBL 2.1 XML for ZATCA Phase 2
     */
    public function generateXml(SalesInvoice $invoice, ZatcaSetting $setting): string
    {
        $hasValidBuyerVat = !empty($invoice->customer->vat_number) && preg_match('/^3[0-9]{13}3$/', preg_replace('/\s+/', '', $invoice->customer->vat_number));
        $internalSubtype = $invoice->invoice_subtype_code ?? ($hasValidBuyerVat ? '1000' : '0100');
        
        // Map Internal to ZATCA Subtype (Standard: 01, Simplified: 02)
        // ZATCA requires 7 characters (0100000 for Standard, 0200000 for Simplified)
        if (in_array((string)$internalSubtype, ['0100', '0200000'])) {
            $subtypeCode = '0200000'; // Simplified Tax Invoice
        } else {
            $subtypeCode = '0100000'; // Standard Tax Invoice (for 1000, 1100, 0100000)
        }
        $isSimplified = ($subtypeCode === '0200000');
        
        $typeCode = $invoice->invoice_type_code ?? '388';
        if (empty($invoice->invoice_type_code)) {
            if ($invoice->type_inv == SalesInvoice::TYPE_RETURN || $invoice->type_inv == SalesInvoice::TYPE_RETURN_POS) {
                $typeCode = '381';
            } elseif ($invoice->type_inv == SalesInvoice::TYPE_DEBIT_NOTE) {
                $typeCode = '383';
            }
        }

        $profileID = 'reporting:1.0';

        $xml = UXML::newInstance('Invoice', null, [
            'xmlns' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            'xmlns:cac' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            'xmlns:cbc' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'xmlns:ext' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'
        ]);

        $xml->add('ext:UBLExtensions');
        $xml->add('cbc:ProfileID', $profileID);
        $xml->add('cbc:ID', $invoice->invoice_number);
        $xml->add('cbc:UUID', $invoice->zatcaDetails->uuid ?? (string) \Illuminate\Support\Str::uuid());
        $xml->add('cbc:IssueDate', $invoice->issue_date->format('Y-m-d'));
        $xml->add('cbc:IssueTime', $invoice->issue_date->format('H:i:s'));
        $xml->add('cbc:InvoiceTypeCode', $typeCode, ['name' => $subtypeCode]);
        
        // KSA-10: MANDATORY FOR CREDIT (381) / DEBIT (383) NOTES
        if ($typeCode !== '388') {
            $reason = ($typeCode === '381') ? 'Returning goods or services already supplied' : 'Correction of error in previous invoice';
            $xml->add('cbc:Note', $reason);
        }

        $xml->add('cbc:DocumentCurrencyCode', 'SAR');
        $xml->add('cbc:TaxCurrencyCode', 'SAR');

        if ($invoice->parent_id && ($typeCode === '381' || $typeCode === '383')) {
            $parent = SalesInvoice::find($invoice->parent_id);
            if ($parent) {
                $ref = $xml->add('cac:BillingReference')->add('cac:InvoiceDocumentReference');
                $ref->add('cbc:ID', $parent->invoice_number);
                $ref->add('cbc:UUID', $parent->zatcaDetails->uuid ?? $parent->uuid ?? '');
                $ref->add('cbc:IssueDate', $parent->issue_date->format('Y-m-d'));
            }
        }

        $xml->add('cac:AdditionalDocumentReference')->add('cbc:ID', 'ICV')->parent()->add('cbc:UUID', (string)($invoice->zatcaDetails->icv ?? 1));
        $xml->add('cac:AdditionalDocumentReference')->add('cbc:ID', 'PIH')->parent()->add('cac:Attachment')->add('cbc:EmbeddedDocumentBinaryObject', $invoice->zatcaDetails->previous_invoice_hash ?? 'NWZlY2ViOTZmOTYyNDY4OGI4OWUwMjk3YmJmNzA0YjY0MWFmNTdlMGRhN2I1MGJhMTgzYmUyYTMxYTBhY2UwOQ==', ['mimeCode' => 'text/plain']);
        $xml->add('cac:AdditionalDocumentReference')->add('cbc:ID', 'QR')->parent()->add('cac:Attachment')->add('cbc:EmbeddedDocumentBinaryObject', 'TEMP_QR_VALUE', ['mimeCode' => 'text/plain']);

        $xml->add('cac:Signature')->add('cbc:ID', 'urn:oasis:names:specification:ubl:signature:Invoice')->parent()->add('cbc:SignatureMethod', 'urn:oasis:names:specification:ubl:dsig:enveloped:xades');

        // Seller info
        $seller = $xml->add('cac:AccountingSupplierParty')->add('cac:Party');
        $sellerCr = !empty($setting->cv) ? $setting->cv : (\App\Models\Organization::first()?->CR ?? '');
        $sellerCr = preg_replace('/[^0-9]/', '', $sellerCr);
        if (!empty($sellerCr) && strlen($sellerCr) >= 10) {
            $seller->add('cac:PartyIdentification')->add('cbc:ID', $sellerCr, ['schemeID' => 'CRN']);
        } else {
            throw new \Exception(__('invoices::models/invoices_setting.fields.zatca_error_cr_required') ?: 'السجل التجاري (CR) مطلوب وصالح (10 أرقام على الأقل) للربط مع هيئة الزكاة والضريبة والجمارك.');
        }

        $address = $seller->add('cac:PostalAddress');
        $address->add('cbc:StreetName', $setting->street_name ?? 'Street');
        $address->add('cbc:BuildingNumber', str_pad(preg_replace('/[^0-9]/', '', $setting->building_number ?? '0000'), 4, '0', STR_PAD_LEFT));
        $address->add('cbc:PlotIdentification', '0000');
        $address->add('cbc:CitySubdivisionName', $setting->district_name ?? 'District');
        $address->add('cbc:CityName', $setting->city_name ?? 'City');
        $address->add('cbc:PostalZone', str_pad(preg_replace('/[^0-9]/', '', $setting->postal_code ?? '00000'), 5, '0', STR_PAD_LEFT));
        $address->add('cac:Country')->add('cbc:IdentificationCode', 'SA');
        $seller->add('cac:PartyTaxScheme')->add('cbc:CompanyID', $setting->vat_number)->parent()->add('cac:TaxScheme')->add('cbc:ID', 'VAT');
        $seller->add('cac:PartyLegalEntity')->add('cbc:RegistrationName', $setting->organization_name);

        // Buyer info
        $buyer = $xml->add('cac:AccountingCustomerParty')->add('cac:Party');
        if ($invoice->customer) {
            $bAddress = $buyer->add('cac:PostalAddress');
            $bAddress->add('cbc:StreetName', $invoice->customer->street ?? 'Street');
            $bAddress->add('cbc:BuildingNumber', str_pad(preg_replace('/[^0-9]/', '', $invoice->customer->building_number ?? '0000'), 4, '0', STR_PAD_LEFT));
            $bAddress->add('cbc:PlotIdentification', '0000');
            $bAddress->add('cbc:CitySubdivisionName', $invoice->customer->district ?? 'District');
            $bAddress->add('cbc:CityName', $invoice->customer->city ?? 'City');
            $bAddress->add('cbc:PostalZone', str_pad(preg_replace('/[^0-9]/', '', $invoice->customer->postal_code ?? '00000'), 5, '0', STR_PAD_LEFT));
            $bAddress->add('cac:Country')->add('cbc:IdentificationCode', 'SA');
            if ($hasValidBuyerVat) {
                $buyer->add('cac:PartyTaxScheme')->add('cbc:CompanyID', preg_replace('/\s+/', '', $invoice->customer->vat_number))->parent()->add('cac:TaxScheme')->add('cbc:ID', 'VAT');
            }
            $buyer->add('cac:PartyLegalEntity')->add('cbc:RegistrationName', $invoice->customer->name);
        } else {
            $buyer->add('cac:PartyLegalEntity')->add('cbc:RegistrationName', 'Cash Customer');
        }

        // KSA-5: Supply Date (Mandatory for Standard Invoice)
        $delivery = $xml->add('cac:Delivery');
        $delivery->add('cbc:ActualDeliveryDate', $invoice->issue_date->format('Y-m-d'));

        // PaymentMeans
        if ($invoice->payments && $invoice->payments->count() > 0) {

           
            foreach ($invoice->payments as $invoicePayment) {
                $paymentMeansCode = !empty($invoicePayment->payment_method_code) 
                    ? (string) $invoicePayment->payment_method_code 
                    : '30'; // Fallback to Cash if payment exists but has no code

                $payment = $xml->add('cac:PaymentMeans');
                $payment->add('cbc:PaymentMeansCode', $paymentMeansCode);
                if ($typeCode !== '388') {
                    $reason = ($typeCode === '381') ? 'Returning goods or services already supplied' : 'Correction of error in previous invoice';
                    $payment->add('cbc:InstructionNote', $reason);
                }
            }
            
        } else {
            
            $payment = $xml->add('cac:PaymentMeans');
            $payment->add('cbc:PaymentMeansCode', '30');
            if ($typeCode !== '388') {
                $reason = ($typeCode === '381') ? 'Returning goods or services already supplied' : 'Correction of error in previous invoice';
                $payment->add('cbc:InstructionNote', $reason);
            }
        }

        $docDiscount = round((float)($invoice->total_discount ?? 0), 2);
        $shippingCost = round((float)($invoice->shipping_cost ?? 0), 2);
        $shippingVatRate = (float)($invoice->shipping_vat_rate ?? 0);

        if ($docDiscount > 0) {
            $allowance = $xml->add('cac:AllowanceCharge');
            $allowance->add('cbc:ChargeIndicator', 'false');
            $allowance->add('cbc:AllowanceChargeReasonCode', '95');
            $allowance->add('cbc:AllowanceChargeReason', 'Discount');
            $allowance->add('cbc:Amount', number_format($docDiscount, 2, '.', ''), ['currencyID' => 'SAR']);
            $allowanceTax = $allowance->add('cac:TaxCategory');
            $allowanceTax->add('cbc:ID', 'S');
            $allowanceTax->add('cbc:Percent', number_format(15, 2, '.', ''));
            $allowanceTax->add('cac:TaxScheme')->add('cbc:ID', 'VAT');
        }

        if ($shippingCost > 0) {
            $charge = $xml->add('cac:AllowanceCharge');
            $charge->add('cbc:ChargeIndicator', 'true');
            $charge->add('cbc:AllowanceChargeReasonCode', 'FC');
            $charge->add('cbc:AllowanceChargeReason', 'Shipping');
            $charge->add('cbc:Amount', number_format($shippingCost, 2, '.', ''), ['currencyID' => 'SAR']);
            $chargeTax = $charge->add('cac:TaxCategory');
            $chargeTax->add('cbc:ID', $shippingVatRate > 0 ? 'S' : 'Z');
            $chargeTax->add('cbc:Percent', number_format($shippingVatRate, 2, '.', ''));
            $chargeTax->add('cac:TaxScheme')->add('cbc:ID', 'VAT');
        }

        $rates = [];
        $totalLineExt = 0;
        foreach ($invoice->items as $item) {
            $ext = round((float)$item->quantity * (float)$item->unit_price, 2);
            $totalLineExt += $ext;
            $rate = (float)$item->vat_rate;
            if (!isset($rates[$rate])) { $rates[$rate] = ['taxable' => 0, 'vat' => 0]; }
            $rates[$rate]['taxable'] += $ext;
        }

        foreach ($rates as $r => &$a) {
            $ratio = $totalLineExt > 0 ? ($a['taxable'] / $totalLineExt) : 0;
            $portion = round($docDiscount * $ratio, 2);
            $sPortion = ($r == $shippingVatRate) ? $shippingCost : 0;
            $a['taxable_net'] = round($a['taxable'] - $portion + $sPortion, 2);
            $a['vat'] = round($a['taxable_net'] * ($r / 100), 2);
        }

        $finalVatTotal = 0;
        foreach ($rates as $a) { $finalVatTotal += $a['vat']; }

        $xml->add('cac:TaxTotal')->add('cbc:TaxAmount', number_format($finalVatTotal, 2, '.', ''), ['currencyID' => 'SAR']);
        $taxTotalDetailed = $xml->add('cac:TaxTotal');
        $taxTotalDetailed->add('cbc:TaxAmount', number_format($finalVatTotal, 2, '.', ''), ['currencyID' => 'SAR']);
        foreach ($rates as $r => $a) {
            if ($a['taxable_net'] <= 0 && $r > 0) continue;
            $sub = $taxTotalDetailed->add('cac:TaxSubtotal');
            $sub->add('cbc:TaxableAmount', number_format($a['taxable_net'], 2, '.', ''), ['currencyID' => 'SAR']);
            $sub->add('cbc:TaxAmount', number_format($a['vat'], 2, '.', ''), ['currencyID' => 'SAR']);
            $cat = $sub->add('cac:TaxCategory');
            $cat->add('cbc:ID', ($r > 0 ? 'S' : 'Z'));
            $cat->add('cbc:Percent', number_format($r, 2, '.', ''));
            if ($r == 0) {
                $cat->add('cbc:TaxExemptionReasonCode', 'VATEX-SA-32');
                $cat->add('cbc:TaxExemptionReason', 'Export of goods');
            }
            $cat->add('cac:TaxScheme')->add('cbc:ID', 'VAT');
        }

        $taxExclusive = round($totalLineExt - $docDiscount + $shippingCost, 2);
        $taxInclusive = round($taxExclusive + $finalVatTotal, 2);

        $monetary = $xml->add('cac:LegalMonetaryTotal');
        $monetary->add('cbc:LineExtensionAmount', number_format($totalLineExt, 2, '.', ''), ['currencyID' => 'SAR']);
        $monetary->add('cbc:TaxExclusiveAmount', number_format($taxExclusive, 2, '.', ''), ['currencyID' => 'SAR']);
        $monetary->add('cbc:TaxInclusiveAmount', number_format($taxInclusive, 2, '.', ''), ['currencyID' => 'SAR']);
        $monetary->add('cbc:AllowanceTotalAmount', number_format($docDiscount, 2, '.', ''), ['currencyID' => 'SAR']);
        $monetary->add('cbc:ChargeTotalAmount', number_format($shippingCost, 2, '.', ''), ['currencyID' => 'SAR']);
        $monetary->add('cbc:PrepaidAmount', '0.00', ['currencyID' => 'SAR']);
        $monetary->add('cbc:PayableAmount', number_format($taxInclusive, 2, '.', ''), ['currencyID' => 'SAR']);

        foreach ($invoice->items as $idx => $item) {
            $lExt = round((float)$item->quantity * (float)$item->unit_price, 2);
            $lVat = round($lExt * ((float)$item->vat_rate / 100), 2);
            $line = $xml->add('cac:InvoiceLine');
            $line->add('cbc:ID', (string)($idx + 1));
            $line->add('cbc:InvoicedQuantity', number_format($item->quantity, 2, '.', ''), ['unitCode' => 'PCE']);
            $line->add('cbc:LineExtensionAmount', number_format($lExt, 2, '.', ''), ['currencyID' => 'SAR']);
            $lTax = $line->add('cac:TaxTotal');
            $lTax->add('cbc:TaxAmount', number_format($lVat, 2, '.', ''), ['currencyID' => 'SAR']);
            $lTax->add('cbc:RoundingAmount', number_format($lExt + $lVat, 2, '.', ''), ['currencyID' => 'SAR']);
            $det = $line->add('cac:Item');
            $det->add('cbc:Name', $item->product_name);
            $cId = ((float)$item->vat_rate > 0) ? 'S' : 'Z';
            $iCat = $det->add('cac:ClassifiedTaxCategory');
            $iCat->add('cbc:ID', $cId);
            $iCat->add('cbc:Percent', number_format($item->vat_rate, 2, '.', ''));
            if ($cId === 'Z') { $iCat->add('cbc:TaxExemptionReasonCode', 'VATEX-SA-32'); }
            $iCat->add('cac:TaxScheme')->add('cbc:ID', 'VAT');
            $line->add('cac:Price')->add('cbc:PriceAmount', number_format($item->unit_price, 2, '.', ''), ['currencyID' => 'SAR']);
        }

        return $xml->asXML();
    }
}

