<div class="web-content">
    <div class="col-12">
        <!-- Header Info -->
        <div class="row g-5 mb-6">
            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <label class="fs-6 fw-bold text-muted mb-1">@lang('accusoft::models/as_journal_entries.fields.entry_number')</label>
                    <div class="fs-5 fw-bolder text-dark">{{ $journalEntry->entry_number ?? '#' . $journalEntry->id }}
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <label class="fs-6 fw-bold text-muted mb-1">@lang('accusoft::models/as_journal_entries.fields.entry_date')</label>
                    <div class="fs-5 fw-bolder text-dark">
                        {{ $journalEntry->entry_date ? \Carbon\Carbon::parse($journalEntry->entry_date)->format('Y-m-d') : '-' }}
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="d-flex flex-column">
                    <label class="fs-6 fw-bold text-muted mb-1">@lang('accusoft::models/as_journal_entries.fields.entry_type')</label>
                    <div>
                        <span class="fs-7 fw-bold">
                            {{ $journalEntry->type_text }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex flex-column">
                    <label class="fs-6 fw-bold text-muted mb-1">@lang('accusoft::models/as_journal_entries.fields.source')</label>
                    <div>
                        <span class="badge {{ $journalEntry->source_badge_class }} fw-semibold fs-7">
                            {{ $journalEntry->source_text }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex flex-column">
                    <label class="fs-6 fw-bold text-muted mb-1">@lang('accusoft::models/as_journal_entries.fields.status')</label>
                    <div>
                        {{ $journalEntry->status_text }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="row mb-8">
            <div class="col-12">
                <label class="fs-6 fw-bold text-muted mb-2">@lang('accusoft::models/as_journal_entries.fields.description')</label>
                <div class="p-4 bg-light rounded border border-gray-300 text-gray-800 fs-6">
                    {{ $journalEntry->description ?? __('lang.no_description') }}
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <div class="card shadow-sm border border-gray-300">
            <div class="card-header bg-light py-2 min-h-50px">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-5 text-gray-800">@lang('lang.details')</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-row-bordered align-middle gs-0 gy-4 mb-0">
                        <thead class="border-bottom border-gray-200 fs-6 fw-bold bg-light-primary">
                            <tr class="text-start text-center  text-gray-800 text-uppercase gs-0">
                                <th class="min-w-50px ps-4">#</th>
                                <th class="min-w-200px">@lang('accusoft::models/as_journal_entries.details.tree_account_id')</th>
                                <th class="min-w-150px">@lang('accusoft::models/as_journal_entries.details.cost_center_id')</th>
                                <th class="min-w-250px">@lang('accusoft::models/as_journal_entries.details.description')</th>
                                <th class="min-w-125px text-end text-success">@lang('accusoft::models/as_journal_entries.details.debit')</th>
                                <th class="min-w-125px text-end text-danger pe-4">@lang('accusoft::models/as_journal_entries.details.credit')</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($journalEntry->details as $index => $detail)
                                <tr>
                                    <td class="text-center ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-gray-800 fw-bold mb-1">{{ $detail->treeAccount->name ?? '-' }}</span>
                                            @if (isset($detail->treeAccount->code))
                                                <span class="text-muted fs-7">{{ $detail->treeAccount->code }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-info fs-7">
                                            {{ $detail->costCenter->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $detail->description }}</td>
                                    <td class="text-end text-success fw-bold fs-6">
                                        {{ number_format($detail->debit, 2) }}</td>
                                    <td class="text-end text-danger fw-bold fs-6 pe-4">
                                        {{ number_format($detail->credit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light fw-bold fs-6 text-gray-800 border-top border-gray-300">
                            <tr>
                                <td colspan="4" class="text-end ps-4 fs-5">@lang('accusoft::models/as_journal_entries.fields.total')</td>
                                <td class="text-end text-success fs-5">
                                    {{ number_format($journalEntry->total_debit, 2) }}</td>
                                <td class="text-end text-danger fs-5 pe-4">
                                    {{ number_format($journalEntry->total_credit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>


            </div>
        </div>

        <div class="col-sm-12 row">
            @if ($journalEntry->attachment_path)
                <div class="col-12">
                    @php
                        $filePath = $journalEntry->attachment_path;
                        $fileName = basename($filePath);
                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                    @endphp


                    <!-- File Information -->
                    <div class="card mb-3">
                        <div class="card-body">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <!-- File Icon -->
                                    <i class="fas fa-file-{{ $fileExtension }} fa-2x text-primary me-3"></i>

                                    <!-- File Details -->
                                    <div>
                                        <p><a href="{{ $journalEntry->attachment_path }}" target="_blank"
                                                class="mb-1"><strong>{{ $fileName }}</strong></a></p>
                                        <small class="text-muted">
                                            {{ $fileExtension ? strtoupper($fileExtension) . ' File' : 'Unknown Format' }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Download Button -->
                                {{-- <a href="{{ route('download-attachment', ['path' => base64_encode($filePath)]) }}"
                           class="btn btn-sm btn-primary"
                           download>
                            <i class="fas fa-download me-2"></i>تحميل
                        </a> --}}
                            </div>
                        </div>
                    </div>

                    <!-- File Preview (للملفات المدعومة) -->
                    @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                        <div class="card">

                            <div class="card-body text-center">
                                @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                    <img src="{{ asset($filePath) }}" alt="{{ $fileName }}" class="img-fluid"
                                        style="max-height: 400px;">
                                @elseif(strtolower($fileExtension) === 'pdf')
                                    <iframe src="{{ asset($filePath) }}#toolbar=0" width="100%" height="600px"
                                        frameborder="0">
                                    </iframe>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="col-8">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>

                    </div>
                </div>
            @endif
        </div>








        <!-- Footer Info -->
        <div
            class="d-flex justify-content-between align-items-center mt-8 pt-4 border-top border-gray-300 text-muted fs-7">
            <div>
                <i class="fa-solid fa-user me-1"></i>
                @lang('accusoft::models/as_journal_entries.fields.created_by'):
                <span class="text-gray-800 fw-bold">{{ $journalEntry->creator->name ?? '-' }}</span>
                <span class="mx-1">•</span>
                <i class="fa-regular fa-clock me-1"></i>
                {{ $journalEntry->created_at->format('Y-m-d h:i A') }}
            </div>

            @if ($journalEntry->status == 'posted')
                <div>
                    <i class="fa-solid fa-check-double me-1 text-success"></i>
                    @lang('accusoft::models/as_journal_entries.fields.posted_at'):
                    <span
                        class="text-gray-800 fw-bold">{{ $journalEntry->posted_at ? \Carbon\Carbon::parse($journalEntry->posted_at)->format('Y-m-d h:i A') : '-' }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 0;
        }

        /* Force high contrast and hide web UI */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        #kt_app_sidebar,
        #kt_app_header,
        #kt_app_toolbar,
        #kt_app_footer,
        .btn,
        .icon-btn,
        .breadcrumb,
        .alert,
        .card-header,
        .no-print,
        .col-sm-12.row,
        .web-content {
            display: none !important;
        }

        body,
        .app-wrapper,
        .app-main,
        .app-content,
        .container-xxl,
        .card,
        .card-body {
            background-color: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
        }

        /* Luxury Voucher Container - Optimized for A4 */
        .luxury-voucher {
            display: block !important;
            border: 2px solid #000;
            padding: 10mm;
            margin: 0 !important;
            width: 190mm !important;
            /* Standard A4 width minus some margin */
            height: 277mm !important;
            /* Standard A4 height minus some margin */
            box-sizing: border-box !important;
            position: relative;
            background: #fff;
            overflow: hidden;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Header Layout */
        .v-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-left {
            text-align: left;
            width: 35%;
        }

        .header-right {
            text-align: right;
            width: 35%;
        }

        .header-center {
            text-align: center;
            width: 30%;
        }

        .company-title {
            font-size: 24px;
            font-weight: 900;
            color: #000;
            margin-bottom: 5px;
        }

        .company-subtitle {
            font-size: 12px;
            color: #444;
        }

        .voucher-type-title {
            border: 2px solid #000;
            padding: 10px 30px;
            font-size: 22px;
            font-weight: bold;
            display: inline-block;
            background: #f0f0f0 !important;
        }

        /* Info Box */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-box {
            border: 1px solid #000;
            padding: 10px;
            background: #fafafa !important;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .info-row b {
            color: #000;
        }

        /* Professional Table */
        .v-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .v-table th {
            background: #000 !important;
            color: #fff !important;
            border: 1px solid #000;
            padding: 12px 5px;
            font-size: 13px;
            text-align: center;
        }

        .v-table td {
            border: 1px solid #000;
            padding: 10px 8px;
            text-align: center;
            font-size: 12px;
        }

        .v-table .text-right {
            text-align: right;
        }

        .v-table .bg-gray {
            background: #f5f5f5 !important;
        }

        /* Notes Section */
        .notes-box {
            border: 1px solid #000;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            min-height: 50px;
        }

    }

    /* Web Visibility */
    .luxury-voucher {
        display: none;
    }
</style>

{{-- The Luxury Voucher Markup --}}
<div class="luxury-voucher">
    <div class="v-header">
        <div class="header-right">
            @if (!empty($org->logo))
                <img src="{{ asset($org->logo) }}" style="max-height: 80px; margin-bottom: 10px;">
            @endif
            <div class="company-title">{{ $org->name ?? 'مؤسسة إيفكس للأنظمة' }}</div>
            <div class="company-subtitle">
                @if ($org->tax_number)
                    <div>@lang('models/Organization.fields.tax_number'): {{ $org->tax_number }}</div>
                @endif
                @if ($org->CR)
                    <div>@lang('models/Organization.fields.CR'): {{ $org->CR }}</div>
                @endif
            </div>
        </div>

        <div class="header-center">
            <div class="voucher-type-title">@lang('accusoft::models/as_journal_entries.singular')</div>
            <div style="margin-top: 10px; font-weight: bold;">
                @lang('accusoft::models/as_journal_entries.fields.original_voucher')
            </div>
        </div>
        <div class="header-left">
            <div style="font-size: 14px; line-height: 2;">
                <div><strong>@lang('accusoft::models/as_journal_entries.fields.entry_number'):</strong> <span
                        style="font-size: 14px; border: 1px solid #000; padding: 2px 10px; margin-right: 5px;">{{ $journalEntry->entry_number ?? $journalEntry->id }}</span>
                </div>
                <div><strong>@lang('accusoft::models/as_journal_entries.fields.entry_date'):</strong>
                    {{ $journalEntry->entry_date ? \Carbon\Carbon::parse($journalEntry->entry_date)->format('Y-m-d') : date('Y-m-d') }}
                </div>
                <div><strong>@lang('accusoft::models/as_journal_entries.fields.branch_id'):</strong> {{ $journalEntry->branch->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <div class="info-row"><b>@lang('accusoft::models/as_journal_entries.fields.entry_type'):</b> <span>{{ $journalEntry->type_text }}</span></div>
            <div class="info-row"><b>@lang('accusoft::models/as_journal_entries.fields.status'):</b> <span>{{ $journalEntry->status_text }}</span></div>
        </div>
        <div class="info-box">

            <div class="info-row"><b>@lang('accusoft::models/as_journal_entries.fields.accountant'):</b> <span>{{ $journalEntry->creator->name ?? '-' }}</span>
            </div>
        </div>
    </div>

    <table class="v-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">@lang('accusoft::models/as_journal_entries.fields.account_code')</th>
                <th width="25%">@lang('accusoft::models/as_journal_entries.fields.account_id') / @lang('accusoft::models/as_journal_entries.fields.description')</th>
                <th width="20%">@lang('accusoft::models/as_journal_entries.details.description')</th>
                <th width="15%">@lang('accusoft::models/as_journal_entries.details.debit')</th>
                <th width="15%">@lang('accusoft::models/as_journal_entries.details.credit')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($journalEntry->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->treeAccount->code }}</td>
                    <td class="text-right">
                        <b>{{ $detail->treeAccount->name }}</b>
                        @if ($detail->costCenter)
                            <br><small>@lang('accusoft::models/as_journal_entries.fields.cost_center_id'): {{ $detail->costCenter->name }}</small>
                        @endif
                    </td>
                    <td class="text-right">{{ $detail->description }}</td>
                    <td style="font-weight: bold;">{{ number_format($detail->debit, 2) }}</td>
                    <td style="font-weight: bold;">{{ number_format($detail->credit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"
                    style="font-size: 16px; font-weight: 900; background: #eee !important;">@lang('accusoft::models/as_journal_entries.fields.total')</td>
                <td style="background: #eee !important; font-size: 16px; font-weight: 900;">
                    {{ number_format($journalEntry->total_debit, 2) }}</td>
                <td style="background: #eee !important; font-size: 16px; font-weight: 900;">
                    {{ number_format($journalEntry->total_credit, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"
                    style="background: #fff !important; padding: 10px; border: 1px solid #000;">
                    <b>@lang('accusoft::models/as_journal_entries.fields.amount_in_words'):</b>
                    <span id="tafqeet-total">
                        {{ number_format($journalEntry->total_debit, 2) }}
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>

    <div
        style="font-weight: bold; margin-top: -15px; font-size: 12px; background: #f0f0f0 !important; padding: 5px; border: 1px solid #000;">
        @lang('accusoft::models/as_journal_entries.fields.description'): {{ $journalEntry->description ?? '-' }}
    </div>

</div>
