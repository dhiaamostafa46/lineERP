<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة نقاط بيع</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        .header { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .header h2 { margin: 0 0 5px 0; font-size: 14px; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-size: 10px; }
        .info-table td { padding: 2px 0; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; border-bottom: 1px dashed #000; }
        .items-table th { border-bottom: 1px dashed #000; border-top: 1px dashed #000; padding: 4px 0; }
        .items-table td { padding: 4px 0; vertical-align: top; }
        
        .totals-table { width: 100%; font-size: 11px; margin-bottom: 10px; }
        .totals-table td { padding: 2px 0; }
        .totals-table .grand-total { font-weight: bold; font-size: 13px; }
        
        .qr-section { text-align: center; margin: 10px 0; }
        .qr-section img { max-width: 120px; }
        
        .footer { text-align: center; font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>

    <div class="header text-center">
        @php
            $org = \App\Models\Organization::first();
        @endphp
        <h2>{{ $org->name_ar ?? 'مؤسسة إيفكس' }}</h2>
        <p>{{ $invoice->store->name ?? 'الفرع الرئيسي' }}</p>
        <p>الرقم الضريبي: {{ $org->vat_number ?? '---' }}</p>
        <p>فاتورة ضريبية مبسطة</p>
    </div>

    <table class="info-table">
        <tr>
            <td>رقم الفاتورة:</td>
            <td class="font-bold">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td>التاريخ:</td>
            <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('Y-m-d h:i A') }}</td>
        </tr>
        <tr>
            <td>الكاشير:</td>
            <td>{{ $invoice->createdBy->name ?? '---' }}</td>
        </tr>
        @if($invoice->customer && $invoice->customer->name != 'Default Customer')
        <tr>
            <td>العميل:</td>
            <td>{{ $invoice->customer->name }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-right">الصنف</th>
                <th class="text-center">الكمية</th>
                <th class="text-center">السعر</th>
                <th class="text-left">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td class="text-right">{{ $item->product->name ?? 'صنف غير معروف' }}</td>
                <td class="text-center">{{ (int)$item->qty }}</td>
                <td class="text-center">{{ number_format($item->price, 2) }}</td>
                <td class="text-left">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>الإجمالي غير شامل الضريبة:</td>
            <td class="text-left">{{ number_format($invoice->total_exclusive_vat, 2) }}</td>
        </tr>
        <tr>
            <td>مجموع ضريبة القيمة المضافة:</td>
            <td class="text-left">{{ number_format($invoice->total_vat, 2) }}</td>
        </tr>
        @if($invoice->total_discount > 0)
        <tr>
            <td>إجمالي الخصم:</td>
            <td class="text-left">{{ number_format($invoice->total_discount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="2"><hr style="border-top:1px dashed #000;"></td>
        </tr>
        <tr class="grand-total">
            <td>الإجمالي (شامل الضريبة):</td>
            <td class="text-left">{{ number_format($invoice->total_inclusive_vat, 2) }} ريال</td>
        </tr>
    </table>

    @if(isset($invoice->qr_code) && !empty($invoice->qr_code))
    <div class="qr-section">
        <img src="data:image/png;base64, {{ $invoice->qr_code }}" alt="QR Code" />
    </div>
    @endif

    <div class="footer">
        <p>شكراً لزيارتكم</p>
        <p>نظام Evix ERP</p>
    </div>

</body>
</html>
