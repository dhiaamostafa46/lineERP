<?php

namespace Modules\Invoices\App\Services;

use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\ZatcaSetting;
use Illuminate\Support\Facades\Log;
use Exception;

class ZatcaPhase2Service
{
    protected $zatcaService;
    protected $zatcaXmlService;

    public function __construct(ZatcaService $zatcaService, ZatcaXmlService $zatcaXmlService)
    {
        $this->zatcaService = $zatcaService;
        $this->zatcaXmlService = $zatcaXmlService;
    }

    /**
     * Process and Report any Invoice to ZATCA Phase 2
     */
    public function processAndReport(SalesInvoice $invoice, ZatcaSetting $setting): array
    {
        try {
            // 1. Determine if B2B (Standard) or B2C (Simplified)
            $isB2B = in_array((string)$invoice->invoice_subtype_code, ['1000', '1100']);

            // 2. Prepare ICV and PIH
            $this->prepareSequentialData($invoice);

            // 3. Generate Signed XML and Hash
            $signedData = $this->zatcaService->generatePhase2QrCode($invoice, $setting);

            // 4. Send to ZATCA (Compliance check or Reporting/Clearance)
            $apiResult = null;
            $uuid = $invoice->zatcaDetails->uuid;

            if ($setting->status == ZatcaSetting::ZATCA_STATUS_LINKED) {
                // Testing phase
                $apiResult = $this->zatcaService->checkCompliance(
                    $uuid,
                    $signedData['hash'],
                    $signedData['signed_xml'],
                    $setting
                );
            } else {
                // Production phase
                $apiResult = $this->zatcaService->reportInvoice(
                    $uuid,
                    $signedData['hash'],
                    $signedData['signed_xml'],
                    $setting,
                    $isB2B
                );
            }

            return [
                'success' => true,
                'api_result' => $apiResult,
                'signed_data' => $signedData
            ];
        } catch (Exception $e) {
            Log::error('ZatcaPhase2Service Error: ' . $e->getMessage(), ['invoice_id' => $invoice->id]);
            throw $e;
        }
    }

    /**
     * Handle Sequential Data (ICV & PIH)
     */
    protected function prepareSequentialData(SalesInvoice $invoice)
    {
        $lastInvoice = SalesInvoice::where('branch_id', $invoice->branch_id)
            ->where('id', '<', $invoice->id)
            ->whereNotNull('qr_code')
            ->whereHas('zatcaDetails', function ($q) {
                $q->where('icv', '>', 0);
            })
            ->with('zatcaDetails')
            ->orderBy('id', 'desc')
            ->first();

        $icv = $lastInvoice ? ($lastInvoice->zatcaDetails->icv + 1) : 1;
        $pih = $lastInvoice ? $lastInvoice->zatcaDetails->previous_invoice_hash : 'NWZlY2ViOTZmOTYyNDY4OGI4OWUwMjk3YmJmNzA0YjY0MWFmNTdlMGRhN2I1MGJhMTgzYmUyYTMxYTBhY2UwOQ==';

        $invoice->zatcaDetails()->updateOrCreate(
            ['sales_invoice_id' => $invoice->id],
            [
                'uuid' => $invoice->zatcaDetails->uuid ?? (string) \Illuminate\Support\Str::uuid(),
                'icv'  => $icv,
                'previous_invoice_hash' => $pih
            ]
        );

        $invoice->load('zatcaDetails');
    }

    /**
     * Specialized: Report Standard Invoice (B2B)
     */
    public function reportStandardInvoice(SalesInvoice $invoice, ZatcaSetting $setting)
    {
        if (empty($invoice->customer->vat_number)) {
            throw new Exception('Customer VAT number is required for Standard Invoices.');
        }
        return $this->processAndReport($invoice, $setting);
    }

    /**
     * Specialized: Report Simplified Invoice (B2C)
     */
    public function reportSimplifiedInvoice(SalesInvoice $invoice, ZatcaSetting $setting)
    {
        return $this->processAndReport($invoice, $setting);
    }

    /**
     * Specialized: Report Credit Note (Return)
     */
    public function reportCreditNote(SalesInvoice $invoice, ZatcaSetting $setting)
    {
        if ($invoice->type_inv != SalesInvoice::TYPE_RETURN && $invoice->type_inv != SalesInvoice::TYPE_RETURN_POS) {
            throw new Exception('Document must be a return to issue a Credit Note.');
        }
        return $this->processAndReport($invoice, $setting);
    }

    /**
     * Specialized: Report Debit Note
     */
    public function reportDebitNote(SalesInvoice $invoice, ZatcaSetting $setting)
    {
        // Add specific debit note logic if needed
        return $this->processAndReport($invoice, $setting);
    }
}

