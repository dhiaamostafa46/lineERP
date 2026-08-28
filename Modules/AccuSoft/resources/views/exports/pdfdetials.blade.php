<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">

    <style>
        /* إعداد الصفحة */
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .container {
            border: 1px solid #eee;
            padding: 10px;
        }

        /* ===== Header ===== */
        .header-table td {
            vertical-align: top;
        }

        .company-info h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .company-info p {
            font-size: 12px;
            line-height: 1.6;
        }

        .logo img {
            max-width: 120px;
        }

        /* ===== Title ===== */
        .entry-title {
            text-align: center;
            margin: 15px 0;
        }

        .entry-title span {
            font-size: 18px;
            font-weight: bold;
            border: 1px solid #ddd;
            padding: 6px 20px;
            /* background: #f8f9fa; */
        }

        /* ===== Entry Info ===== */
        .entry-info td {
            border: 1px solid #ddd;
            padding: 8px;
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        /* ===== Journal Table ===== */
        .journal-table th,
        .journal-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .journal-table th {
            background: #e9ecef;
        }

        .journal-table tfoot td {
            background: #e9ecef;
            font-weight: bold;
        }

        /* ===== Description ===== */
        .description {
            border: 1px solid #ddd;
            padding: 8px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }

        /* ===== Signatures ===== */
        .signatures td {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td width="70%" class="company-info">
                <h2>{{ $org->name ?? '' }}</h2>
                <p><strong>@lang('models/Organization.fields.tax_number'):</strong> {{ $org->tax_number ?? '' }}</p>
                <p><strong>@lang('models/Organization.fields.CR'):</strong> {{ $org->CR ?? '' }}</p>
                <p><strong>@lang('models/Organization.fields.national_address'):</strong> {{ $org->national_address ?? '' }}</p>
            </td>
            <td width="30%" class="logo" style="text-align:left;">
                @if (!empty($org->logo))
                    <img src="{{ public_path($org->logo) }}">
                @endif
            </td>
        </tr>
    </table>

    <!-- Title -->
    <div class="entry-title">
        <span>
            @lang('accusoft::models/as_journal_entries.singular')
            #{{ $journalEntry->entry_number ?? ($journalEntry->id ?? '-') }}
        </span>
    </div>

    <!-- Entry Info -->
    <table class="entry-info">
        <tr>
            <td>
                @lang('accusoft::models/as_journal_entries.fields.entry_date'):
                {{ $journalEntry->entry_date ?? ($journalEntry->date ?? date('Y-m-d')) }}
            </td>
            <td>
                @lang('accusoft::models/as_journal_entries.fields.entry_type'):
                {{ $journalEntry->type_text ?? '-' }}
            </td>
            <td>
                @lang('accusoft::models/as_journal_entries.fields.status'):
                {{ $journalEntry->status_text ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- Description -->
    @if (!empty($journalEntry->description))
        <div class="description">
            <strong>@lang('accusoft::models/as_journal_entries.fields.description'):</strong>
            {{ $journalEntry->description }}
        </div>
    @endif

    <!-- Journal Table -->
    <table class="journal-table">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">@lang('accusoft::models/as_journal_entries.fields.total')</td>
                <td>{{ number_format($journalEntry->total_debit ?? 0, 2) }}</td>
                <td>{{ number_format($journalEntry->total_credit ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <table class="signatures" style="margin-top:30px;">
        <tr>
            <td width="50%">
                @lang('accusoft::models/as_journal_entries.fields.created_by')
            </td>
            <td width="50%">
                @lang('accusoft::models/as_journal_entries.fields.approved_by')
            </td>
        </tr>

        <!-- مسافة مضمونة -->
        <tr>
            <td height="30"></td>
            <td height="30"></td>
        </tr>

        <tr>
            <td>___________________________</td>
            <td>___________________________</td>
        </tr>
    </table>

</div>

</body>
</html>
