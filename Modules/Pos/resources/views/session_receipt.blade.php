<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Z-Report تقرير إغلاق الوردية</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');
        
        body {
            font-family: 'Tajawal', 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f8f9fa;
        }
        
        .receipt-container {
            width: 80mm;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            box-sizing: border-box;
            border: 1px solid #ddd;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #555; }
        
        .header { margin-bottom: 12px; text-align: center; }
        .header h2 { margin: 0 0 4px 0; font-size: 16px; font-weight: 800; }
        .header p { margin: 2px 0; font-size: 12px; }
        .header .z-badge { 
            display: inline-block; 
            border: 2px solid #000; 
            padding: 4px 10px; 
            font-weight: 800; 
            margin-top: 5px;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .line-divider { border-top: 1px dashed #000; margin: 10px 0; }
        .thick-divider { border-top: 2px solid #000; margin: 12px 0; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-size: 11px; }
        .info-table td { padding: 3px 0; }
        .info-table td:first-child { width: 40%; font-weight: 700; }
        
        .totals-table { width: 100%; font-size: 12px; margin-bottom: 10px; border-collapse: collapse; }
        .totals-table td { padding: 5px 2px; border-bottom: 1px dotted #ccc; }
        .totals-table tr:last-child td { border-bottom: none; }
        .totals-table .grand-total td { font-weight: 800; font-size: 14px; border-bottom: none; padding-top: 8px;}
        .totals-table .section-title { font-weight: 800; background: #f0f0f0; text-align: center; font-size: 13px; padding: 6px; border-bottom: none !important;}
        
        .variance-row td { font-weight: 800; font-size: 13px; }
        .success-text { color: #000; } /* Keep black for thermal */
        .danger-text { color: #000; }
        
        .footer { text-align: center; font-size: 11px; margin-top: 15px; }
        .signature-box { display: flex; justify-content: space-between; margin-top: 25px; margin-bottom: 10px; }
        .signature-line { width: 45%; border-top: 1px dashed #000; text-align: center; padding-top: 5px; font-size: 10px;}
        
        @media print {
            @page { size: 80mm auto; margin: 0; }
            body { background: transparent; }
            .receipt-container { 
                width: 80mm !important; 
                margin: 0 auto !important; 
                padding: 5mm !important; 
                box-shadow: none !important; 
                border: none !important; 
            }
        }
    </style>
</head>
<body>

<div class="receipt-container">
    <div class="header">
        @php
            $org = \App\Models\Organization::first();
        @endphp
        <h2>{{ app()->getLocale() == 'ar' ? ($org->name_ar ?? 'مؤسسة إيفكس') : ($org->name_en ?? 'Evix Organization') }}</h2>
        <p class="font-bold">{{ $session->device->store->name ?? '---' }}</p>
        <p>{{ __('pos::lang.device') }}: {{ $session->device->name ?? '---' }}</p>
        <div class="z-badge">Z - REPORT</div>
    </div>

    <table class="info-table">
        <tr>
            <td>{{ __('pos::lang.session') }}:</td>
            <td class="font-bold text-left">#{{ $session->id }}</td>
        </tr>
        <tr>
            <td>{{ __('pos::lang.cashier') }}:</td>
            <td class="text-left">{{ $session->cashier->name ?? '---' }}</td>
        </tr>
        <tr>
            <td>{{ __('pos::lang.opened_at') }}:</td>
            <td class="text-left">{{ \Carbon\Carbon::parse($session->opened_at)->format('Y-m-d h:i A') }}</td>
        </tr>
        <tr>
            <td>{{ __('pos::lang.closed_at') }}:</td>
            <td class="text-left">{{ \Carbon\Carbon::parse($session->closed_at)->format('Y-m-d h:i A') }}</td>
        </tr>
    </table>

    <div class="thick-divider"></div>

    @php
        // Calculate totals based on transactions (Returns and Withdrawals are stored as negative)
        $totalSales = $session->transactions->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE)->sum('amount');
        $totalReturns = abs($session->transactions->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN)->sum('amount'));
        $totalDeposits = $session->transactions->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_DEPOSIT)->sum('amount');
        $totalWithdrawals = abs($session->transactions->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_WITHDRAWAL)->sum('amount'));
        
        // Group by payment method for SALES
        $salesByMethod = [];
        foreach($session->transactions->where('type', \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE) as $tx) {
            $methodName = $tx->paymentMethod->name ?? 'طريقة دفع';
            if(!isset($salesByMethod[$methodName])) {
                $salesByMethod[$methodName] = 0;
            }
            $salesByMethod[$methodName] += $tx->amount;
        }

        // Calculate actual invoiced amounts to find unpaid (Credit/Agel) sales
        $invoices = \App\Models\invApp\SalesInvoice::where('pos_session_id', $session->id)->get();
        $totalInvoicesSales = 0;
        $totalInvoicesReturns = 0;
        foreach ($invoices as $invoice) {
            if ($invoice->type_inv == \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS || $invoice->type_inv == \App\Models\invApp\SalesInvoice::TYPE_RETURN) {
                $totalInvoicesReturns += $invoice->total_inclusive_vat;
            } else {
                $totalInvoicesSales += $invoice->total_inclusive_vat;
            }
        }
        
        $unpaidSales = max(0, $totalInvoicesSales - $totalSales);
        $unpaidReturns = max(0, $totalInvoicesReturns - $totalReturns);
        
        if ($unpaidSales > 0) {
            $salesByMethod['آجل (ذمم عملاء)'] = $unpaidSales;
            $totalSales += $unpaidSales; // Include in grand total
        }
        
        if ($unpaidReturns > 0) {
            $totalReturns += $unpaidReturns; // Include in grand total
        }

        // Cash calculations
        $cashSales = 0;
        $cashReturns = 0;
        $cashDeposits = 0;
        $cashWithdrawals = 0;

        foreach($session->transactions as $tx) {
            if(isset($tx->paymentMethod) && strtolower($tx->paymentMethod->type) == 'cash') {
                if ($tx->type == \Modules\Pos\App\Models\PosSessionTransaction::TYPE_SALE) $cashSales += $tx->amount;
                elseif ($tx->type == \Modules\Pos\App\Models\PosSessionTransaction::TYPE_RETURN) $cashReturns += abs($tx->amount);
                elseif ($tx->type == \Modules\Pos\App\Models\PosSessionTransaction::TYPE_DEPOSIT) $cashDeposits += $tx->amount;
                elseif ($tx->type == \Modules\Pos\App\Models\PosSessionTransaction::TYPE_WITHDRAWAL) $cashWithdrawals += abs($tx->amount);
            }
        }
        
        // Dynamic Expected Cash Calculation
        $expectedCash = $session->opening_balance + $cashSales - $cashReturns + $cashDeposits - $cashWithdrawals;
        $actualCash = $session->actual_cash ?? 0;
        
        if ($session->closed_at) {
            $variance = $session->difference ?? 0;
        } else {
            $variance = $actualCash - $expectedCash;
        }
        
        $dyn_shortage = $variance < 0 ? abs($variance) : 0;
        $dyn_overage = $variance > 0 ? $variance : 0;
    @endphp

    <table class="totals-table">
        <tr>
            <td colspan="2" class="section-title">ملخص إيرادات الوردية</td>
        </tr>
        @foreach($salesByMethod as $method => $amount)
        <tr>
            <td>مبيعات ({{ $method }}):</td>
            <td class="text-left font-bold">{{ number_format($amount, 2) }}</td>
        </tr>
        @endforeach
        
        @if($totalReturns > 0)
        <tr>
            <td>إجمالي المرتجعات:</td>
            <td class="text-left font-bold danger-text">-{{ number_format($totalReturns, 2) }}</td>
        </tr>
        @endif
        
        @if($totalDeposits > 0)
        <tr>
            <td>إيداعات في الدرج:</td>
            <td class="text-left font-bold">+{{ number_format($totalDeposits, 2) }}</td>
        </tr>
        @endif
        
        @if($totalWithdrawals > 0)
        <tr>
            <td>سحوبات / مصروفات:</td>
            <td class="text-left font-bold danger-text">-{{ number_format($totalWithdrawals, 2) }}</td>
        </tr>
        @endif
        
        <tr class="grand-total">
            <td style="border-top: 1px solid #000;">صافي الإيرادات:</td>
            <td class="text-left" style="border-top: 1px solid #000;">{{ number_format($totalSales - $totalReturns, 2) }}</td>
        </tr>
    </table>

    <div class="line-divider"></div>

    <table class="totals-table">
        <tr>
            <td colspan="2" class="section-title">المطابقة النقدية للدرج (Cash)</td>
        </tr>
        <tr>
            <td>رصيد البداية (العهدة):</td>
            <td class="text-left">{{ number_format($session->opening_balance, 2) }}</td>
        </tr>
        <tr>
            <td>مبيعات نقدية:</td>
            <td class="text-left">+{{ number_format($cashSales, 2) }}</td>
        </tr>
        
        @if($cashReturns > 0)
        <tr>
            <td>مرتجعات نقدية:</td>
            <td class="text-left danger-text">-{{ number_format($cashReturns, 2) }}</td>
        </tr>
        @endif
        
        @if($cashDeposits > 0)
        <tr>
            <td>إيداعات نقدية إضافية:</td>
            <td class="text-left">+{{ number_format($cashDeposits, 2) }}</td>
        </tr>
        @endif
        
        @if($cashWithdrawals > 0)
        <tr>
            <td>سحوبات نقدية (مصروف):</td>
            <td class="text-left danger-text">-{{ number_format($cashWithdrawals, 2) }}</td>
        </tr>
        @endif
        
        <tr style="background: #f8f9fa;">
            <td class="font-bold">الإجمالي المتوقع (النظام):</td>
            <td class="text-left font-bold">{{ number_format($expectedCash, 2) }}</td>
        </tr>
        <tr>
            <td class="font-bold">المبلغ الفعلي (المدخل):</td>
            <td class="text-left font-bold">{{ number_format($actualCash, 2) }}</td>
        </tr>
        
        @if($dyn_shortage > 0)
        <tr class="variance-row">
            <td class="danger-text" style="border-top: 1px solid #000; padding-top: 8px;">عجز بالصندوق:</td>
            <td class="text-left danger-text" style="border-top: 1px solid #000; padding-top: 8px;">-{{ number_format($dyn_shortage, 2) }}</td>
        </tr>
        @elseif($dyn_overage > 0)
        <tr class="variance-row">
            <td class="success-text" style="border-top: 1px solid #000; padding-top: 8px;">زيادة بالصندوق:</td>
            <td class="text-left success-text" style="border-top: 1px solid #000; padding-top: 8px;">+{{ number_format($dyn_overage, 2) }}</td>
        </tr>
        @else
        <tr class="variance-row">
            <td colspan="2" class="text-center" style="border-top: 1px solid #000; padding-top: 8px;">
                الرصيد مطابق تماماً (لا يوجد عجز/زيادة)
            </td>
        </tr>
        @endif
    </table>

    @if($session->notes)
    <div class="line-divider"></div>
    <div style="font-size: 11px; margin-top: 10px;">
        <span class="font-bold">ملاحظات الإغلاق:</span><br>
        <span class="text-muted">{{ $session->notes }}</span>
    </div>
    @endif

    <div class="signature-box">
        <div class="signature-line">توقيع الكاشير</div>
        <div class="signature-line">توقيع المشرف</div>
    </div>

    <div class="footer text-muted">
        تمت الطباعة: {{ now()->format('Y-m-d h:i A') }}<br>
        <span class="font-bold">Evix ERP - POS System</span>
    </div>

</div>

</body>
</html>
