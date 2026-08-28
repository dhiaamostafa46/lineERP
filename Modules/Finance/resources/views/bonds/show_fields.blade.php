<div class="web-content">
<!--begin::Summary Cards-->
<div class="row g-5 g-xl-10 mb-10">
    <div class="col-md-4">
        <div class="card h-100 border border-dashed border-primary bg-light-primary">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <span class="fs-6 fw-semibold text-gray-600 mb-1">@lang('finance::models/fnc_bond.fields.amount')</span>
                <span class="fs-2hx fw-bold text-primary">{{ number_format($bond->amount, 2) }} <small class="fs-4"></small></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border border-dashed border-gray-300">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <span class="fs-6 fw-semibold text-gray-600 mb-1">@lang('finance::models/fnc_bond.fields.date')</span>
                <span class="fs-2 fw-bold text-gray-800">{{ $bond->date ? \Carbon\Carbon::parse($bond->date)->format('Y-m-d') : '-' }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border border-dashed border-gray-300">
            <div class="card-body d-flex flex-column justify-content-center text-center">
                <span class="fs-6 fw-semibold text-gray-600 mb-1">@lang('finance::models/fnc_bond.fields.status')</span>
                <div>
                    <span class="badge {{ $bond->status_badge ?? 'badge-light-primary' }} fs-5 px-4 py-3">
                        {{ $bond->status_text ?? $bond->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Summary Cards-->

<div class="row g-9">
    <!-- الحسابات والأطراف -->
    <div class="col-md-6">
        <div class="d-flex align-items-center mb-5">
            <i class="ki-outline ki-wallet fs-2 text-primary me-2"></i>
            <h4 class="fw-bold text-gray-800 mb-0">@lang('finance::models/fnc_bond.sections.account_details')</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <tbody>
                    <tr>
                        <td class="text-muted min-w-150px">@lang('finance::models/fnc_bond.fields.bond_type')</td>
                        <td class="text-gray-800 fw-bold">{{ $bond->bond_type_text ?? $bond->bond_type }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('finance::models/fnc_bond.fields.fund_account_id')</td>
                        <td class="text-gray-800 fw-bold">{{ $bond->fundAccount?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('finance::models/fnc_bond.fields.contact_account_id')</td>
                        <td class="text-gray-800 fw-bold">{{ $bond->contactAccount?->name ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- معلومات إضافية -->
    <div class="col-md-6">
        <div class="d-flex align-items-center mb-5">
            <i class="ki-outline ki-setting-3 fs-2 text-primary me-2"></i>
            <h4 class="fw-bold text-gray-800 mb-0">@lang('finance::models/fnc_bond.sections.other_info')</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <tbody>
                    <tr>
                        <td class="text-muted min-w-150px">@lang('finance::models/fnc_bond.fields.reference_number')</td>
                        <td class="text-gray-800">{{ $bond->reference_number ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('finance::models/fnc_bond.fields.branch_id')</td>
                        <td class="text-gray-800">{{ $bond->branch?->name ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('finance::models/fnc_bond.fields.cost_center_id')</td>
                        <td class="text-gray-800">{{ $bond->costCenter?->name ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- الوصف والمرفقات -->
    <div class="col-12">
        <div class="separator separator-dashed border-gray-300 my-8"></div>
        <div class="row g-9">
            <div class="col-md-7">
                <h5 class="fw-bold text-gray-800 mb-4">
                    <i class="ki-outline ki-message-text-2 fs-3 text-primary me-2"></i>
                    @lang('finance::models/fnc_bond.fields.description')
                </h5>
                <div class="bg-light p-5 rounded border border-gray-200 text-gray-700 fs-6" style="min-height: 100px;">
                    {{ $bond->description ?: __('lang.no_description') }}
                </div>
            </div>
            <div class="col-md-5">
                <h5 class="fw-bold text-gray-800 mb-4">
                    <i class="ki-outline ki-paper-clip fs-3 text-primary me-2"></i>
                    @lang('finance::models/fnc_bond.fields.attachment')
                </h5>

                @if($bond->attachment)
                    <a href="{{  $bond->attachment_url }}" target="_blank" class="d-flex align-items-center p-3 border border-dashed border-gray-400 rounded text-gray-800 text-hover-primary bg-hover-light">
                        <i class="ki-outline ki-file fs-2x me-3 text-primary"></i>
                        <span class="fw-bold"> </span>
                    </a>
                @else
                    <span class="text-muted fs-7 italic">@lang('lang.no_attachment')</span>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

{{-- LUXURY PRINT DESIGN --}}
<style>
    @media print {
        @page { size: A4; margin: 0; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        
        #kt_app_sidebar, #kt_app_header, #kt_app_toolbar, #kt_app_footer, 
        .btn, .icon-btn, .breadcrumb, .alert, .card-header, .no-print, .web-content {
            display: none !important;
        }

        body, .app-wrapper, .app-main, .app-content, .container-xxl, .card, .card-body {
            background-color: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
        }

        body { font-family: 'Amiri', serif; direction: rtl; }

        .luxury-bond {
            display: block !important;
            border: 2px solid #000;
            padding: 10mm;
            margin: 0 auto !important;
            width: 190mm !important;
            height: 277mm !important;
            box-sizing: border-box !important;
            position: relative;
            background: #fff;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .bond-title-badge {
            background: #000 !important;
            color: #fff !important;
            padding: 10px 40px;
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
        }

        .field-group {
            display: flex;
            margin-bottom: 15px;
            border: 1px solid #000;
            background: #f9f9f9 !important;
        }

        .field-label {
            background: #eee !important;
            padding: 10px;
            width: 150px;
            font-weight: bold;
            border-left: 1px solid #000;
        }

        .field-value {
            padding: 10px;
            flex-grow: 1;
        }

        .amount-box {
            border: 2px solid #000;
            padding: 15px;
            font-size: 24px;
            font-weight: 900;
            display: inline-block;
            background: #f0f0f0 !important;
            margin-bottom: 20px;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-bottom: 10px;
        }
    }
    .luxury-bond { display: none; }
</style>

<div class="luxury-bond">
    <div class="header-section">
        <div style="width: 35%">
            @if(!empty($org->logo))
                <img src="{{ $org->logo_original_path }}" style="max-height: 80px; margin-bottom: 10px;">
            @endif
            <div style="font-size: 20px; font-weight: bold;">{{ $org->name ?? 'مؤسسة إيفكس' }}</div>
            <div style="font-size: 11px;">
                @if($org->tax_number) <div>@lang('models/Organization.fields.tax_number'): {{ $org->tax_number }}</div> @endif
                @if($org->CR) <div>@lang('models/Organization.fields.CR'): {{ $org->CR }}</div> @endif
            </div>
        </div>
        <div style="width: 30%; text-align: center;">
            <div class="bond-title-badge">
                {{ $bond->bond_type_text }}
            </div>
            <div style="margin-top: 5px; font-weight: bold;">{{ $bond->bond_type_text ?? 'سند' }}</div>
        </div>
        <div style="width: 35%; text-align: left;">
            <div style="font-size: 14px; line-height: 2;">
                <div><strong>@lang('finance::models/fnc_bond.fields.voucher_number'):</strong> <span style="font-size: 14px; border: 1px solid #000; padding: 2px 10px;">{{ $bond->voucher_number }}</span></div>
                <div><strong>@lang('finance::models/fnc_bond.fields.date'):</strong> {{ $bond->date ? \Carbon\Carbon::parse($bond->date)->format('Y-m-d') : date('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 30px;">
        <div class="amount-box">
            {{ number_format($bond->amount, 2) }} SAR
        </div>
    </div>

    <div class="field-group">
        <div class="field-label">@lang('finance::models/fnc_bond.fields.pay_to_receive_from'):</div>
        <div class="field-value" style="font-size: 18px; font-weight: bold;">{{ $bond->contactAccount?->name ?? '..........................................................' }}</div>
    </div>

    <div class="field-group">
        <div class="field-label">@lang('finance::models/fnc_bond.fields.amount_text'):</div>
        <div class="field-value">{{ number_format($bond->amount, 2) }} @lang('finance::models/fnc_bond.fields.sar_only').</div>
    </div>

    <div class="field-group">
        <div class="field-label">@lang('finance::models/fnc_bond.fields.for_purpose'):</div>
        <div class="field-value">{{ $bond->description ?? '..........................................................' }}</div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
        <div class="field-group">
            <div class="field-label">@lang('finance::models/fnc_bond.fields.fund_account_id'):</div>
            <div class="field-value">{{ $bond->fundAccount?->name }}</div>
        </div>
        <div class="field-group">
            <div class="field-label">@lang('finance::models/fnc_bond.fields.reference_number'):</div>
            <div class="field-value">{{ $bond->reference_number ?: '-' }}</div>
        </div>
    </div>

</div>

