<?php

namespace Modules\Invoices\App\Services;

use Salla\ZATCA\GenerateCSR;
use Salla\ZATCA\Models\CSRRequest;
use Salla\ZATCA\Helpers\Certificate;
use Salla\ZATCA\Models\InvoiceSign;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Helpers\UXML;
use App\Models\invApp\SalesInvoice;
use Modules\Invoices\App\Models\ZatcaSetting;
use App\Models\Organization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ZatcaService
{
    /**
     * Get ZATCA API Base URL based on environment.
     */
    protected function getBaseUrl(string $environment): string
    {
        return match ($environment) {
            'sandbox'    => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal',
            'simulation' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation',
            'production' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core',
            default      => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal',
        };
    }

    /**
     * Generate CSR and Private Key.
     */
    public function generateCSR(ZatcaSetting $setting): array
    {
        try {
            // Validate required fields
            $requiredFields = [
                'vat_number' => 'الرقم الضريبي',
                'common_name' => 'الاسم الشائع (Common Name)',
                'organization_name' => 'اسم المنشأة',
                'organization_unit_name' => 'اسم الفرع',
                'registered_address' => 'العنوان المسجل',
                'activity_classification' => 'تصنيف النشاط'
            ];

            foreach ($requiredFields as $field => $label) {
                if (empty($setting->$field)) {
                    throw new Exception(__('invoices::models/invoices_setting.fields.zatca_error_required', ['field' => $label]));
                }
            }

            // Validate VAT Number (15 digits, starts and ends with 3)
            if (!preg_match('/^3[0-9]{13}3$/', $setting->vat_number)) {
                throw new Exception(__('invoices::models/invoices_setting.fields.zatca_error_vat_format'));
            }

            $isB2B = true;
            $isB2C = true;

            if ($setting->inv_type == '0100') {
                $isB2B = false;
                $isB2C = true;
            } elseif ($setting->inv_type == '1000') {
                $isB2B = true;
                $isB2C = false;
            } else {
                // Default or 1100 (Both)
                $isB2B = true;
                $isB2C = true;
            }

            $data = CSRRequest::make()
                ->setUID($setting->vat_number ?? '')
                ->setSerialNumber('NewEvixERP', '1.0', $setting->uuid ?? uniqid())
                ->setCommonName($setting->common_name ?? '')
                ->setCountryName('SA')
                ->setOrganizationName($setting->organization_name ?? '')
                ->setOrganizationalUnitName($setting->organization_unit_name ?? '')
                ->setRegisteredAddress($setting->registered_address ?? '')
                ->setInvoiceType($isB2B, $isB2C)
                ->setCurrentZatcaEnv($setting->environment ?? 'sandbox')
                ->setBusinessCategory($setting->activity_classification ?? 'Software');

            $csrObj = GenerateCSR::fromRequest($data)->initialize()->generate();

            // تصدير المفتاح الخاص كـ "نص" ليتم حفظه
            $privateKey = '';

            // حل مشكلة الويندوز: تحديد مسار ملف openssl.cnf
            $configArgs = null;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $commonPaths = [
                    'C:/xampp/php/extras/ssl/openssl.cnf',
                    'C:/Program Files/Common Files/SSL/openssl.cnf',
                    'C:/usr/local/ssl/openssl.cnf',
                    'C:/php/extras/ssl/openssl.cnf'
                ];
                foreach ($commonPaths as $path) {
                    if (file_exists($path)) {
                        $configArgs = ['config' => $path];
                        break;
                    }
                }
            }

            if (!openssl_pkey_export($csrObj->getPrivateKey(), $privateKey, null, $configArgs)) {
                $errorMsg = 'فشل تصدير المفتاح الخاص. ';
                if ($configArgs === null && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $errorMsg .= 'لم يتم العثور على ملف openssl.cnf على جهاز الويندوز. يرجى تثبيت XAMPP أو توفير الملف.';
                } else {
                    $errorMsg .= openssl_error_string() ?: 'تأكد من إعدادات OpenSSL.';
                }
                throw new Exception($errorMsg);
            }

            $csrContent = $csrObj->getCsrContent();

            // 1. الحفظ في قاعدة البيانات
            $setting->update([
                'private_key' => $privateKey,
                'csr'         => $csrContent,
            ]);

            // 2. الحفظ في ملفات (كما في الطريقة الأصلية للمكتبة)
            try {
                $storagePath = storage_path('app/zatca/' . ($setting->vat_number ?? 'default'));
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                // حفظ المفتاح الخاص والشهادة
                file_put_contents($storagePath . '/private_key.pem', $privateKey);
                file_put_contents($storagePath . '/request.csr', $csrContent);

                Log::info('ZATCA Keys saved to files successfully at: ' . $storagePath);
            } catch (\Exception $fileEx) {
                Log::warning('Could not save ZATCA keys to files: ' . $fileEx->getMessage());
            }

            return [
                'private_key' => $privateKey,
                'csr'         => $csrContent
            ];
        } catch (Exception $e) {
            Log::error('ZATCA CSR Generation Error: ' . $e->getMessage());
            throw new Exception(__('messages.error_generating_csr') . ': ' . $e->getMessage());
        }
    }

    /**
     * Onboard with ZATCA (Get Compliance CSID).
     */
    public function onboard(ZatcaSetting $setting, string $otp, string $intendedEnvironment = null)
    {
        $environment = $intendedEnvironment ?? $setting->environment ?? 'sandbox';

        if (empty($setting->csr) || empty($setting->private_key)) {
            $this->generateCSR($setting);
            $setting->refresh();
        }

        // Clean OTP
        $otp = trim($otp);

        $baseUrl = $this->getBaseUrl($environment);
        $endpoint = $baseUrl . '/compliance';

        $response = Http::withHeaders([
            'OTP' => $otp,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Version' => 'V2',
            'Accept-Language' => 'en'
        ])->post($endpoint, [
            'csr' => base64_encode($setting->csr)
        ]);



        if ($response->successful()) {
            $data = $response->json();

            $setting->update([
                'binary_security_token' => $data['binarySecurityToken'] ?? $data['binary_security_token'] ?? null,
                'secret'                => $data['secret'] ?? null,
                'request_id'            => $data['requestID'] ?? $data['request_id'] ?? null,
                'status'                => ZatcaSetting::ZATCA_STATUS_LINKED, // Standard linked status
                'environment'           => $environment,
                'is_active'             => true,
            ]);

            return $data;
        }

        $error = $response->json();
        Log::error('ZATCA Onboarding Error', ['response' => $error]);

        $msg = 'Unknown ZATCA Error';
        
        if (isset($error['response']['errors']) && is_array($error['response']['errors'])) {
            $msg = is_string($error['response']['errors'][0]) ? $error['response']['errors'][0] : ($error['response']['errors'][0]['message'] ?? 'Unknown ZATCA Error');
        } elseif (isset($error['errors']) && is_array($error['errors'])) {
            $msg = is_string($error['errors'][0]) ? $error['errors'][0] : ($error['errors'][0]['message'] ?? 'Unknown ZATCA Error');
        } elseif (isset($error['message'])) {
            $msg = $error['message'];
        }

        // Detailed validation errors if present
        if (isset($error['validationResults']['errorMessages'])) {
            $details = collect($error['validationResults']['errorMessages'])->pluck('message')->implode(' | ');
            $msg .= " - Details: " . $details;
        }

        throw new Exception($msg);
    }

    /**
     * Get Production CSID (Final Step for Production Environment).
     * Requires valid Compliance CSID (Binary Security Token & Secret).
     */
    public function getProductionCSID(ZatcaSetting $setting)
    {
        if (empty($setting->binary_security_token) || empty($setting->secret) || empty($setting->request_id)) {
            throw new Exception('يجب إكمال مرحلة اختبارات الامتثال أولاً قبل طلب شهادة الإنتاج.');
        }

        $baseUrl = $this->getBaseUrl($setting->environment);
        $endpoint = $baseUrl . '/production/csids';

        $response = Http::withHeaders([
            'Accept'         => 'application/json',
            'Content-Type'   => 'application/json',
            'Accept-Version' => 'V2',
            'Accept-Language' => 'en',
            'Authorization'  => 'Basic ' . base64_encode($setting->binary_security_token . ':' . $setting->secret),
        ])->post($endpoint, [
            'compliance_request_id' => $setting->request_id
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $setting->update([
                'binary_security_token' => $data['binarySecurityToken'] ?? $data['binary_security_token'] ?? null,
                'secret'                => $data['secret'] ?? null,
                'request_id'            => $data['requestID'] ?? $data['request_id'] ?? null,
                'status'                => ZatcaSetting::ZATCA_STATUS_PRODUCTION, // Final production status
                'is_active'             => true,
            ]);

            return $data;
        }

        $error = $response->json();
        Log::error('ZATCA Production CSID Error', ['response' => $error]);
        throw new Exception('خطأ في استخراج شهادة الإنتاج: ' . ($error['message'] ?? $response->body()));
    }

    /**
     * Sign the XML Invoice using the obtained Certificate and Secret.
     * 
     * @param string $xmlInvoice The pure XML invoice
     * @param ZatcaSetting $setting
     * @return array [ 'hash' => ..., 'signed_xml' => ..., 'qr_code' => ... ]
     */
    public function signInvoice(string $xmlInvoice, ZatcaSetting $setting): array
    {
        if (empty($setting->binary_security_token) || empty($setting->private_key) || empty($setting->secret)) {
            throw new Exception(__('messages.error', [], 'en') . ': ZATCA Settings (Certificate, Private Key, or Secret) are missing. Please link with ZATCA first.');
        }

        $certificate = (new Certificate(
            base64_decode($setting->binary_security_token),
            $setting->private_key
        ))->setSecretKey($setting->secret);

        // Ensure XML is clean and UTF-8
        $xmlInvoice = trim($xmlInvoice);

        $invoiceSigner = new InvoiceSign($xmlInvoice, $certificate);
        $signedInvoice = $invoiceSigner->sign();

        return [
            'hash'       => $signedInvoice->getHash(),
            'signed_xml' => $signedInvoice->getInvoice(),
            'qr_code'    => $signedInvoice->getQRCode(),
        ];
    }

    /**
     * Report an Invoice to ZATCA API (B2C) or Clearance (B2B)
     * 
     * @param string $invoiceUUID
     * @param string $invoiceHash
     * @param string $signedXml
     * @param ZatcaSetting $setting
     * @param bool $isB2B
     * @return array
     */
    public function reportInvoice(string $invoiceUUID, string $invoiceHash, string $signedXml, ZatcaSetting $setting, bool $isB2B = false)
    {
        $baseUrl = $this->getBaseUrl($setting->environment);

        // ZATCA Phase 2: B2B requires Clearance, B2C requires Reporting
        $endpoint = $isB2B
            ? $baseUrl . '/invoices/clearance/single'
            : $baseUrl . '/invoices/reporting/single';

        $payload = [
            'invoiceHash' => $invoiceHash,
            'uuid'        => $invoiceUUID,
            'invoice'     => base64_encode($signedXml),
        ];

        $response = Http::withHeaders([
            'Accept'           => 'application/json',
            'Content-Type'     => 'application/json',
            'Accept-Version'   => 'V2',
            'Accept-Language'  => 'en',
            'Authorization'    => 'Basic ' . base64_encode($setting->binary_security_token . ':' . $setting->secret),
            'Clearance-Status' => $isB2B ? '1' : '0',
        ])->post($endpoint, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        $error = $response->json();
        Log::error('ZATCA API Error', [
            'endpoint' => $endpoint,
            'response' => $error,
            'uuid'     => $invoiceUUID
        ]);

        $msg = $error['message'] ?? ($error['errors'][0]['message'] ?? 'ZATCA API Error');
        if (isset($error['validationResults']['errorMessages'])) {
            $details = collect($error['validationResults']['errorMessages'])->pluck('message')->implode(' | ');
            $msg .= " - Details: " . $details;
        }

        throw new Exception($msg);
    }

    /**
     * Check Compliance (Send test invoices to ZATCA).
     */
    public function checkCompliance(string $invoiceUUID, string $invoiceHash, string $signedXml, ZatcaSetting $setting)
    {
        $baseUrl = $this->getBaseUrl($setting->environment);
        $endpoint = $baseUrl . '/compliance/invoices';

        $payload = [
            'invoiceHash' => $invoiceHash,
            'uuid'        => $invoiceUUID,
            'invoice'     => base64_encode($signedXml),
        ];

        $response = Http::withHeaders([
            'Accept'         => 'application/json',
            'Content-Type'   => 'application/json',
            'Accept-Version' => 'V2',
            'Accept-Language' => 'en',
            'Authorization'  => 'Basic ' . base64_encode($setting->binary_security_token . ':' . $setting->secret),
        ])->post($endpoint, $payload);


        if ($response->successful()) {
            return $response->json();
        }



        Log::error('ZATCA Compliance Check Error', ['response' => $response->json()]);
        throw new Exception('خطأ في اختبار الامتثال: ' . $response->body());
    }

    /**
     * Generate Phase 1 QR Code (Standard TLV)
     * Checks if Phase 2 (ZatcaSetting) is active and linked (branch-specific or unified),
     * otherwise falls back to Organization data.
     */
    public function generatePhase1QrCode($invoice, ?ZatcaSetting $setting = null, bool $asBase64 = true): string
    {
        $organization = Organization::first();
        $branchId = $invoice->branch_id ?? (auth()->user()->branch_id ?? null);

        // 1. Resolve setting if not provided (handles tax_registration_type: branches vs unified)
        if (!$setting) {
            $setting = ZatcaSetting::resolveForBranch($branchId);
        }

        // 2. Check if Phase 2 is active and linked
        $isPhase2 = $setting && $setting->isPhase2Enabled();

        if ($isPhase2) {
            // If Phase 2 is enabled: get branch/setting data
            $sellerName = !empty($setting->organization_name)
                ? $setting->organization_name
                : ($organization?->name ?? config('app.name'));

            $vatNumber  = !empty($setting->vat_number)
                ? $setting->vat_number
                : ($organization?->tax_number ?? '000000000000000');
        } else {
            // If Phase 2 is not enabled: fetch directly from Organization
            $sellerName = $organization?->name ?? config('app.name');
            $vatNumber  = $organization?->tax_number ?? '000000000000000';
        }

        $qrCode = GenerateQrCode::fromArray([
            new Seller($sellerName),
            new TaxNumber($vatNumber),
            new InvoiceDate($invoice->issue_date->toIso8601ZuluString()),
            new InvoiceTotalAmount(number_format($invoice->total_inclusive_vat, 2, '.', '')),
            new InvoiceTaxAmount(number_format($invoice->total_vat, 2, '.', ''))
        ]);

        return $asBase64 ? $qrCode->toBase64() : $qrCode->toTLV();
    }

    /**
     * Handle Phase 2 QR Generation (Sign XML)
     */
    public function generatePhase2QrCode(SalesInvoice $invoice, ZatcaSetting $setting): array
    {
        $xmlService = app(ZatcaXmlService::class);
        $xmlContent = $xmlService->generateXml($invoice, $setting);

        $signedData = $this->signInvoice($xmlContent, $setting);

        return $signedData;
    }
}

