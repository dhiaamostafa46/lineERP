@if (isset($reportData) && !empty($reportData))
    <div class="card shadow-sm mt-5 mb-5" id="report_table">
        <div class="card-header">
            <div class="card-title">
                <h3>@lang('accusoft::models/as_reports.reports.journal_entries')</h3>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-5">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.from_date'):</td>
                            <td>{{ $fromDate ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.to_date'):</td>
                            <td>{{ $toDate ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle gy-5">
                    <thead class="bg-light-primary">
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.entry_number')</th>
                            <th class="text-center min-w-100px">@lang('accusoft::models/as_reports.columns.date')</th>
                            <th class="text-center min-w-100px">@lang('accusoft::models/as_reports.filters.entry_type')</th>
                            <th class="text-center min-w-150px">@lang('accusoft::models/as_reports.filters.account')</th>
                            <th class="text-start min-w-200px">@lang('accusoft::models/as_reports.columns.description')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.debit')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.credit')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData['items'] as $item)
                            <tr>
                                <td class="text-center">
                                    @if (!empty($item['journal_entry_id']))
                                        <a href="{{ route('accusoft.JournalEntry.show', $item['journal_entry_id']) }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                                            {{ $item['entry_number'] }}
                                        </a>
                                    @else
                                        {{ $item['entry_number'] }}
                                    @endif
                                </td>
                                <td class="text-center" dir="ltr">{{ $item['entry_date'] }}</td>
                                <td class="text-center">{{ $item['entry_type'] }}</td>
                                <td class="text-center">
                                    {{ $item['account_name'] }}<br>
                                    <span class="text-muted small">({{ $item['account_code'] }})</span>
                                </td>
                                <td class="text-start">
                                    @if (!empty($item['journal_entry_id']))
                                        <a href="{{ route('accusoft.JournalEntry.show', $item['journal_entry_id']) }}" target="_blank" class="text-dark text-hover-primary text-decoration-none">
                                            {{ $item['description'] ?? '-' }}
                                        </a>
                                    @else
                                        {{ $item['description'] ?? '-' }}
                                    @endif
                                    @if ($item['cost_center'])
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-building"></i>
                                            {{ $item['cost_center'] }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center {{ $item['debit'] > 0 ? 'text-success fw-bold' : '' }}">{{ $item['debit'] }}</td>
                                <td class="text-center {{ $item['credit'] > 0 ? 'text-danger fw-bold' : '' }}">{{ $item['credit'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-10">
                                    <i class="fa-solid fa-inbox fs-3x mb-3 d-block"></i>
                                    @lang('lang.no_data')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($reportData['items']) > 0)
                    <tfoot class="">
                        <tr class="fw-bold fs-6">
                            <th colspan="5" class="text-center">@lang('accusoft::models/as_reports.columns.net_balance') (Total)</th>
                            <th class="text-center text-success">{{ $reportData['total_debit'] }}</th>
                            <th class="text-center text-danger">{{ $reportData['total_credit'] }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            <div class="text-muted text-end mt-3">
                <small>
                    <i class="fa-solid fa-clock"></i>
                    @lang('accusoft::models/as_reports.columns.generated_at'): {{ now()->format('Y-m-d H:i:s') }}
                </small>
            </div>
        </div>
    </div>
@else
    <div class="card shadow-sm mt-5 mb-5">
        <div class="card-body text-center py-20">
            <i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i>
            <h3 class="text-muted">@lang('accusoft::models/as_reports.messages.select_account_description')</h3>
        </div>
    </div>
@endif
