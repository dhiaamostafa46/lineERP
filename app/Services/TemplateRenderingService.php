<?php

namespace App\Services;

use App\Models\Template;
use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\Quotation;
use Modules\Invoices\App\Models\ZatcaSetting;
use Illuminate\Support\Facades\Blade;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TemplateRenderingService
{
    /**
     * Render the document using the dynamic template builder.
     */
    public static function renderDocument($document, $documentType = 'SalesInvoice', $printFormat = 'A4')
    {
        // Fetch the active default template for this document type
        $template = Template::where(function($q) use ($printFormat) {
                $q->where('print_format', $printFormat)
                  ->orWhere('print_format', strtolower($printFormat))
                  ->orWhere('print_format', ucfirst(strtolower($printFormat)));
            })
            ->where('is_default', 1)
            ->activeOnly()
            ->first();

        if (!$template) {
            // Fallback to any default template if specific type not found
            $template = Template::where(function($q) use ($printFormat) {
                $q->where('print_format', $printFormat)
                  ->orWhere('print_format', strtolower($printFormat))
                  ->orWhere('print_format', ucfirst(strtolower($printFormat)));
            })->first();
        }

        if (!$template) {
            return "<div>لا يوجد قالب طباعة محدد. يرجى إعداد القوالب أولاً.</div>";
        }

        $enableEnglish = $template->variables['enable_english'] ?? false;
        
        $previewData = self::mapDocumentToPreviewData($document, $documentType, $enableEnglish);

        return $template->renderPreview($previewData);
    }

    /**
     * Map actual document properties to the format expected by the template builder.
     */
    public static function mapDocumentToPreviewData($document, $documentType = 'SalesInvoice', $enableEnglish = false)
    {
        $org = $document->branch->organization ?? \App\Models\Organization::first();
        $zatcaSetting = ZatcaSetting::resolveForBranch($document->branch_id);
        $customer = $document->customer;
        $isQuotation = $document instanceof Quotation;

        $identifier = $isQuotation ? $document->quotation_number : $document->invoice_number;
        $isSimplified = empty($customer?->vat_number);
        $statusinv =$document->status ?? 1; 

        return array_merge(
            self::buildSellerData($document, $org, $zatcaSetting, $enableEnglish),
            self::buildCustomerData($customer, $enableEnglish),
            self::buildDocumentInfo($document, $isQuotation, $isSimplified, $identifier, $enableEnglish ,$statusinv),
            self::buildItemsAndTotals($document, $enableEnglish),
            self::buildPaymentAndZatcaData($document, $isQuotation)
        );
    }

    /**
     * Parses JSON translation strings based on the enable_english setting.
     */
    private static function parseValue($val, $enableEnglish)
    {
        if (is_string($val) && str_starts_with(trim($val), '{') && str_ends_with(trim($val), '}')) {
            $arr = json_decode($val, true);
            if (is_array($arr)) {
                $ar = $arr['ar'] ?? '';
                $en = $arr['en'] ?? '';
                
                if ($enableEnglish && !empty($ar) && !empty($en) && $ar !== $en) {
                    return $ar . ' - ' . $en;
                }
                return $arr[app()->getLocale()] ?? $ar ?? $en ?? $val;
            }
        }
        return $val;
    }

    /**
     * Builds seller (organization/branch) related data.
     */
    private static function buildSellerData($document, $org, $zatcaSetting, $enableEnglish)
    {
        $sellerName = $zatcaSetting?->organization_name ?? ($org->name ?? '---');
        $sellerName = self::parseValue($sellerName, $enableEnglish);
        $sellerVat = $zatcaSetting?->vat_number ?? ($org->tax_number ?? '---');
        $sellercr = $zatcaSetting?->cv ?? ($org->CR ?? '---');
        
        $sellerAddressParts = array_filter([
            self::parseValue($zatcaSetting?->building_number ?? $document->branch->area ?? '', $enableEnglish),
            self::parseValue($zatcaSetting?->street_name ?? $document->branch->address ?? '', $enableEnglish),
            self::parseValue($zatcaSetting?->district_name ?? $document->branch->district ?? '', $enableEnglish),
            self::parseValue($zatcaSetting?->city_name ?? $document->branch->city ?? '', $enableEnglish)
        ]);
        
        $logo = '';
        if ($org && $org->logo) {
            $logo = $org->logo_original_path;
        }

        return [
            'organization_name' => $sellerName,
            'branch_name' => $document->branch->name ?? '---',
            'seller_vat' => $sellerVat,
            'seller_cr' => $sellercr,
            'seller_phone' => $document->branch->phone ?? ($org->phone ?? '---'),
            'seller_address' => implode('، ', $sellerAddressParts) ?: '---',
            'company_logo' => $logo,
        ];
    }

    /**
     * Builds customer related data.
     */
    private static function buildCustomerData($customer, $enableEnglish)
    {
        $customerAddress = '';
        if ($customer) {
            $customerAddressParts = array_filter([
                self::parseValue($customer->building_number ?? '', $enableEnglish),
                self::parseValue($customer->street ?? '', $enableEnglish),
                self::parseValue($customer->district ?? '', $enableEnglish),
                self::parseValue($customer->city ?? '', $enableEnglish),
                self::parseValue($customer->country ?? '', $enableEnglish),
                self::parseValue($customer->postal_code ?? '', $enableEnglish),
            ]);
            $customerAddress = implode('، ', $customerAddressParts);
        }

        return [
            'customer_name' => $customer->name ?? '---',
            'customer_tax' => $customer->vat_number ?? '---',
            'customer_address_full' => $customerAddress ?: '---',
            'customer_phone' => $customer->phone ?? '---',
            'customer_cr' => $customer->cr_number ?? '---',
        ];
    }

    /**
     * Builds document specific info (titles, identifiers, dates, barcodes).
     */
    private static function buildDocumentInfo($document, $isQuotation, $isSimplified, $identifier, $enableEnglish ,$statusinv)
    {
        $customerInvNumber = $isQuotation ? '' : ($document->parent->invoice_number ?? '---');

        // Document Titles
        if ($isQuotation) {
            $invoiceTitleAr = 'عرض سعر';
            $invoiceTitleEn = 'Quotation';
            $invoiceTypeText = 'عرض سعر';
            $invoiceTypeCode = 'QUO';
        } else {
            if (in_array($document->type_inv, [SalesInvoice::TYPE_RETURN, SalesInvoice::TYPE_RETURN_POS])) {
                $invoiceTitleAr = $statusinv == 1 ?  'مسودة' :  ($isSimplified ? 'إشعار دائن ضريبي مبسط' : 'إشعار دائن ضريبي');
                $invoiceTitleEn = $statusinv == 1 ? 'Draft' :   ($isSimplified ? 'Simplified Tax Credit Note' : 'Tax Credit Note');
                $invoiceTypeCode = '381';
            } elseif ($document->type_inv == SalesInvoice::TYPE_DEBIT_NOTE) {
                $invoiceTitleAr = $statusinv == 1 ?  'مسودة' :  ($isSimplified ? 'إشعار مدين مبسط' : 'إشعار مدين');
                $invoiceTitleEn = $statusinv == 1 ? 'Draft' :   ($isSimplified ? 'Simplified Sales Debit Note' : 'Sales Debit Note');
                $invoiceTypeCode = '383';
            } else {
                $invoiceTitleAr = $statusinv == 1 ?  'مسودة' :  ($isSimplified ? 'فاتورة ضريبية مبسطة' : 'فاتورة ضريبية');
                $invoiceTitleEn = $statusinv == 1 ? 'Draft' :  ($isSimplified ? 'Simplified Tax Invoice' : 'Tax Invoice');
                $invoiceTypeCode = '388';
            }
            $invoiceTypeText = $invoiceTitleAr;
        }

        $invoiceSubtypeCode = $isSimplified ? '0200000' : '0100000';
        $invoiceSubtypeText = $isSimplified ? 'مبسطة' : 'قياسية';

        // Document Barcode
        $barcodeRendered = '';
        if ($identifier) {
            try {
                $generator = new BarcodeGeneratorPNG();
                $base64 = base64_encode($generator->getBarcode($identifier, $generator::TYPE_CODE_128));
                $barcodeRendered = '<img src="data:image/png;base64,' . $base64 . '" alt="Barcode" style="max-width: 250px; height: 40px;">';
            } catch (\Exception $e) { }
        }

        return [
            'invoice_number' => $identifier,
            'order_number' => $document->order_number ?? '',
            'customer_invoice_number' => $customerInvNumber,
            'issue_date' => $document->issue_date ? $document->issue_date->format('Y-m-d') : '',
            'issue_datetime' => $document->issue_date ? $document->issue_date->format('Y-m-d H:i:s') : '',
            
            'invoice_title_ar' => $invoiceTitleAr,
            'invoice_title_en' => $invoiceTitleEn,
            'invoice_type_text' => $invoiceTypeText,
            'invoice_type_code' => $invoiceTypeCode,
            'invoice_subtype_text' => $invoiceSubtypeText,
            'invoice_subtype_code' => $invoiceSubtypeCode,
            
            'barcode_rendered' => $barcodeRendered,
            'created_by_name' => $document->createdBy->name ?? '---',
            'status_text' => $document->status_text ?? '---',
            'invoice_description' => $document->notes??'',
            'notes' => $document->notes ?? '',
            'payment_terms' => $document->payment_terms ?? '',
            'validity_period' => $document->validity_period ?? '',
        ];
    }

    /**
     * Builds items array and totals. Optimized to initialize BarcodeGeneratorPNG once.
     */
    private static function buildItemsAndTotals($document, $enableEnglish)
    {
        $items = [];
        $barcodeGenerator = new BarcodeGeneratorPNG();

        if ($document->items) {
            foreach ($document->items as $item) {
                $taxableAmount = ($item->quantity * $item->unit_price) - ($item->total_discount ?? 0);
                $vatAmount = $item->vat_amount ?? ($item->subtotal_with_vat - $taxableAmount);
                $sku = $item->product ? $item->product->barcode : '';
                
                $itemBarcode = '';
                if ($sku) {
                    try {
                        // Reuse the single BarcodeGenerator instance for better performance
                        $b64 = base64_encode($barcodeGenerator->getBarcode($sku, BarcodeGeneratorPNG::TYPE_CODE_128));
                        $itemBarcode = '<img src="data:image/png;base64,' . $b64 . '" style="height: 25px; max-width: 100px;">';
                    } catch (\Exception $e) {}
                }

                
               // dd($item->unitname?->name);
             
                $items[] = [
                    'product_name' => self::parseValue($item->product_name ?? '---', $enableEnglish),
                    'image' => $item->product?->img_path,
                    'description' => self::parseValue($item->description ?? '', $enableEnglish),
                    'unit_name' => $item->unitname ? self::parseValue($item->unitname?->name, $enableEnglish) : '',
                    'unit_price' => number_format($item->unit_price, 2, '.', ''),
                    'quantity' => number_format($item->quantity, 2, '.', ''),
                    'discount' => number_format($item->total_discount ?? 0, 2, '.', ''),
                    'discount_value' => number_format($item->total_discount ?? 0, 2, '.', ''),
                    'taxable_amount' => number_format($taxableAmount, 2, '.', ''),
                    'vat_rate' => number_format($item->vat_rate ?? 15, 0),
                    'tax_percent' => number_format($item->vat_rate ?? 15, 0),
                    'vat_amount' => number_format($vatAmount, 2, '.', ''),
                    'total' => number_format($item->subtotal_with_vat ?? 0, 2, '.', ''),
                    'barcode' => $sku,
                    'characteristics' => '',
                    'options' => '',
                    'item_barcode_rendered' => $itemBarcode,
                ];
            }
        }


        $shippingCost = $document->shipping_cost ?? 0;
        $shippingVat = $document->shipping_vat_amount ?? 0;
        $shippingTotal = $shippingCost + $shippingVat;

        return [
            'items' => $items,
            'total_exclusive_vat' => number_format($document->total_exclusive_vat, 2, '.', ''),
            'total_discount' => number_format($document->total_discount ?? 0, 2, '.', ''),
            'total_vat' => number_format($document->total_vat ?? 0, 2, '.', ''),
            'shipping_cost' => number_format($shippingCost, 2, '.', ''),
            'shipping_cost_total' => number_format($shippingTotal, 2, '.', ''),
            'total_inclusive_vat' => number_format($document->total_inclusive_vat ?? 0, 2, '.', ''),
            'total_in_words' => self::tafqeet($document->total_inclusive_vat ?? 0),
        ];
    }

    /**
     * Builds payment and ZATCA details including QR Code rendering.
     */
    private static function buildPaymentAndZatcaData($document, $isQuotation)
    {
        // Payment Methods
        $paymentMethods = [];
        if (isset($document->payments) && $document->payments->count() > 0) {
            foreach ($document->payments as $payment) {
                $paymentMethods[] = ($payment->method_text ?? '') . ' (' . ($payment->amount_formatted ?? $payment->amount) . ')';
            }
        }
        $paymentMethodStr = count($paymentMethods) > 0 ? implode(' ، ', $paymentMethods) : 'آجل';

        // QR Code Logic
        $qrCodeRendered = '';
        $isDraft = isset($document->status) && $document->status == 1;
        if (!$isQuotation && !$isDraft && !empty($document->qr_code)) {
            try {
                $qrCodeRendered = '<img src="' . (new \chillerlan\QRCode\QRCode)->render($document->qr_code) . '" alt="QR Code" style="width: 100%; height: 100%;">';
            } catch (\Exception $e) { }
        }

        return [
            'payment_method' => $paymentMethodStr,
            'payment_status' => '---',
            'qr_code_rendered' => $qrCodeRendered,
            'qr_code' => (!$isQuotation && !$isDraft) ? ($document->qr_code ?? '') : '',
            
            'zatca_details' => $document->zatcaDetails ? '1' : '0',
            'zatca_request_id' => $document->zatcaDetails?->request_id ?? '',
            'zatca_response_payload' => $document->zatcaDetails?->response_payload ?? '',
            'status_badge' => $document->status_badge ?? 'badge bg-secondary',
        ];
    }

    /**
     * Convert a numeric amount to Arabic words (Tafqeet)
     */
    private static function tafqeet($number)
    {
        if ($number == 0) {
            return 'صفر ريال';
        }

        $units = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        $tens = ['', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $hundreds = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

        // Standardize number format to 2 decimal places
        $number = number_format($number, 2, '.', '');
        $parts = explode('.', $number);
        
        $integerPart = (int)$parts[0];
        $decimalPart = (int)($parts[1] ?? 0);

        $result = [];

        // 1. Process Integer Part (SR)
        if ($integerPart > 0) {
            if ($integerPart === 1) {
                $result[] = 'ريال واحد';
            } elseif ($integerPart === 2) {
                $result[] = 'ريالان';
            } elseif ($integerPart >= 3 && $integerPart <= 10) {
                $result[] = self::convertNumberToWords($integerPart, $units, $tens, $hundreds) . ' ريالات';
            } else {
                $result[] = self::convertNumberToWords($integerPart, $units, $tens, $hundreds) . ' ريال';
            }
        }

        // 2. Process Decimal Part (Halalas)
        if ($decimalPart > 0) {
            if ($decimalPart === 1) {
                $result[] = 'هللة واحدة';
            } elseif ($decimalPart === 2) {
                $result[] = 'هللتان';
            } elseif ($decimalPart >= 3 && $decimalPart <= 10) {
                $result[] = self::convertNumberToWords($decimalPart, $units, $tens, $hundreds) . ' هللات';
            } else {
                $result[] = self::convertNumberToWords($decimalPart, $units, $tens, $hundreds) . ' هللة';
            }
        }

        if (empty($result)) {
            return 'صفر ريال';
        }

        return 'فقط ' . implode(' و ', $result) . ' لا غير';
    }

    private static function convertNumberToWords($number, $units, $tens, $hundreds)
    {
        if ($number == 0) {
            return '';
        }

        if ($number < 20) {
            return $units[$number];
        }

        if ($number < 100) {
            $tensVal = (int)($number / 10);
            $unitsVal = $number % 10;
            return ($unitsVal > 0 ? $units[$unitsVal] . ' و' : '') . $tens[$tensVal];
        }

        if ($number < 1000) {
            $hundredsVal = (int)($number / 100);
            $remainder = $number % 100;
            return $hundreds[$hundredsVal] . ($remainder > 0 ? ' و' . self::convertNumberToWords($remainder, $units, $tens, $hundreds) : '');
        }

        // Handle thousands, millions, billions
        $scales = [
            1000000000 => ['مليار', 'مليارات', 'ملياراً'],
            1000000 => ['مليون', 'ملايين', 'مليوناً'],
            1000 => ['ألف', 'آلاف', 'ألفاً']
        ];

        foreach ($scales as $limit => $words) {
            if ($number >= $limit) {
                $scaleCount = (int)($number / $limit);
                $remainder = $number % $limit;

                $scaleWord = '';
                if ($scaleCount == 1) {
                    $scaleWord = $words[0];
                } elseif ($scaleCount == 2) {
                    if ($limit == 1000) $scaleWord = 'ألفان';
                    elseif ($limit == 1000000) $scaleWord = 'مليونان';
                    elseif ($limit == 1000000000) $scaleWord = 'ملياران';
                } elseif ($scaleCount >= 3 && $scaleCount <= 10) {
                    $scaleWord = self::convertNumberToWords($scaleCount, $units, $tens, $hundreds) . ' ' . $words[1];
                } else {
                    $scaleWord = self::convertNumberToWords($scaleCount, $units, $tens, $hundreds) . ' ' . $words[0];
                }

                return $scaleWord . ($remainder > 0 ? ' و' . self::convertNumberToWords($remainder, $units, $tens, $hundreds) : '');
            }
        }

        return '';
    }
}

