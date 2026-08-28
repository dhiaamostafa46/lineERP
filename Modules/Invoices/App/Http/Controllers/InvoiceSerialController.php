<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Models\invApp\SalesInvoice;
use App\Models\invApp\SalesInvoiceItem;

class InvoiceSerialController extends AppBaseController
{
    /**
     * معالجة serial للفواتير القديمة
     */
    public function generateSerials(Request $request)
    {
        try {
            // تحقق من الصلاحية إن وجدت
            if (auth()->check()) {
                $this->authorize('invoices.sales.edit');
            }

            // احصل على عدد الفواتير التي بحاجة معالجة
            $totalInvoices = SalesInvoice::whereHas('items', function ($q) {
                $q->whereNull('serial');
            })->count();

            if ($totalInvoices === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'جميع الفواتير تحتوي على serial بالفعل.',
                    'processed' => 0,
                ]);
            }

            $processed = 0;

            SalesInvoice::whereHas('items', function ($q) {
                $q->whereNull('serial');
            })
            ->chunk(100, function ($invoices) use (&$processed) {
                foreach ($invoices as $invoice) {
                    $this->processInvoice($invoice);
                    $processed++;
                }
            });

            return response()->json([
                'success' => true,
                'message' => "تمت معالجة {$processed} فاتورة بنجاح!",
                'processed' => $processed,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ أثناء المعالجة: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض حالة معالجة serial
     */
    public function getStatus()
    {
        $pending = SalesInvoice::whereHas('items', function ($q) {
            $q->whereNull('serial');
        })->count();

        $processed = SalesInvoice::whereHas('items', function ($q) {
            $q->whereNotNull('serial');
        })->count();

        return response()->json([
            'pending' => $pending,
            'processed' => $processed,
            'total' => $pending + $processed,
        ]);
    }

    protected function processInvoice(SalesInvoice $invoice): void
    {
        $items = $invoice->items()->whereNull('serial')->get();
        
        if ($items->isEmpty()) {
            return;
        }

        $usedSerials = $invoice->items()->whereNotNull('serial')->pluck('serial')->toArray();
        
        foreach ($items as $item) {
            // توليد serial عشوائي 6 أرقام
            do {
                $serial = str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($serial, $usedSerials));
            
            $item->update(['serial' => $serial]);
            $usedSerials[] = $serial;
        }
    }
}

