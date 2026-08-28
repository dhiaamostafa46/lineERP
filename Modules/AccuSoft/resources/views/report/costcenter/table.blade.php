@if (isset($datacostcenter) && !empty($datacostcenter))
    <div class="card shadow-sm">
        {{-- معلومات التقرير --}}
        <div class="card-header">
            <div class="card-title">
                <h3>{{ __('accusoft::models/as_reports.reports.cost_centers_report') }}</h3>
            </div>
           
        </div>

        <div class="card-body">
            {{-- معلومات مركز التكلفة والفترة --}}
            <div class="row mb-5">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.cost_center_code'):</td>
                            <td>{{ $datacostcenter['cost_center']['code'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.cost_center_name'):</td>
                            <td>{{ $datacostcenter['cost_center']['name'] }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">

                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.filters.branchId'):</td>
                            <td>{{ $datacostcenter['branch_name'] ?? __('accusoft::models/as_reports.filters.all_branch') }}
                            </td>
                        </tr>

                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.from_date'):</td>
                            <td>{{ $datacostcenter['period']['date_from'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.to_date'):</td>
                            <td>{{ $datacostcenter['period']['date_to'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- جدول الحركات --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle gy-5">
                    <thead class="bg-light-primary">
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th class="text-center min-w-100px">@lang('accusoft::models/as_reports.columns.date')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.entry_number')</th>
                            <th class="text-center min-w-100px">@lang('accusoft::models/as_reports.columns.account_type')</th>
                            <th class="text-center min-w-150px">@lang('accusoft::models/as_reports.columns.account')</th>
                            <th class="text-start min-w-200px">@lang('accusoft::models/as_reports.columns.description')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.debit')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.credit')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($datacostcenter['transactions'] as $transaction)
                            <tr>
                                <td class="text-center">{{ $transaction['date'] }}</td>
                                <td class="text-center">
                                    @if (!empty($transaction['journal_entry_id']))
                                        <a href="{{ route('accusoft.JournalEntry.show', $transaction['journal_entry_id']) }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                                            {{ $transaction['entry_number'] }}
                                        </a>
                                    @else
                                        {{ $transaction['entry_number'] }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $transaction['entry_type'] ?? '-' }}</td>
                                <td class="text-center">
                                    @if (isset($transaction['account']))
                                        <div>{{ $transaction['account']['code'] }}</div>
                                        <small class="text-muted">{{ $transaction['account']['name'] }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-start">
                                    @if (!empty($transaction['journal_entry_id']))
                                        <a href="{{ route('accusoft.JournalEntry.show', $transaction['journal_entry_id']) }}" target="_blank" class="text-dark text-hover-primary text-decoration-none">
                                            {{ $transaction['description'] }}
                                        </a>
                                    @else
                                        {{ $transaction['description'] }}
                                    @endif
                                </td>
                                <td
                                    class="text-center {{ (float) str_replace(',', '', $transaction['debit']) > 0 ? 'text-success fw-bold' : '' }}">
                                    {{ $transaction['debit'] }}
                                </td>
                                <td
                                    class="text-center {{ (float) str_replace(',', '', $transaction['credit']) > 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ $transaction['credit'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-10">
                                    <i class="fa-solid fa-inbox fs-3x mb-3 d-block"></i>
                                    @lang('crud.no_data_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- صف الإجماليات --}}
                    @if (count($datacostcenter['transactions']) > 0)
                        <tfoot class="bg-light-warning">
                            <tr class="fw-bold fs-6">
                                <td colspan="5" class="text-end">@lang('accusoft::models/as_reports.columns.total')</td>
                                <td class="text-center text-success">{{ $datacostcenter['totals']['total_debit'] }}
                                </td>
                                <td class="text-center text-danger">{{ $datacostcenter['totals']['total_credit'] }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- معلومات التوليد --}}
            <div class="text-muted text-end mt-3">
                <small>
                    <i class="fa-solid fa-clock"></i>
                    @lang('accusoft::models/as_reports.columns.generated_at'): {{ $datacostcenter['generated_at'] }}
                </small>
            </div>
        </div>
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-body text-center py-20">
            <i class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i>
            <h3 class="text-muted">@lang('accusoft::models/as_reports.messages.select_cost_center')</h3>
            <p class="text-muted">@lang('accusoft::models/as_reports.messages.select_cost_center_description')</p>
        </div>
    </div>
@endif
