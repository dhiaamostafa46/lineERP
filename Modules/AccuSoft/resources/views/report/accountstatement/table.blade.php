@if (isset($accountstat) && !empty($accountstat))
    <div class="card shadow-sm">
        {{-- معلومات التقرير --}}
        <div class="card-header">
            <div class="card-title">
                <h3>{{ __('accusoft::models/as_reports.reports.account_statement') }}</h3>
            </div>

        </div>

        <div class="card-body">
            {{-- معلومات الحساب والفترة --}}
            <div class="row mb-5">
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.account_code'):</td>
                            <td>{{ $accountstat['account']['code'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.account_name'):</td>
                            <td>{{ $accountstat['account']['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.account_type'):</td>
                            <td>{{ $accountstat['account']['type_text'] }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered table-sm">



                            <tr>
                                <td class="fw-bold">@lang('accusoft::models/as_reports.filters.branchId'):</td>
                                <td>{{ $accountstat['branch_name'] ?? __('accusoft::models/as_reports.filters.all_branch') }} </td>
                            </tr>
                        @if(!empty($accountstat['user_name']))
                            <tr>
                                <td class="fw-bold">@lang('accusoft::models/as_journal_entries.fields.created_by'):</td>
                                <td>{{ $accountstat['user_name'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.from_date'):</td>
                            <td>{{ $accountstat['period']['date_from'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">@lang('accusoft::models/as_reports.columns.to_date'):</td>
                            <td>{{ $accountstat['period']['date_to'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>



            {{-- جدول الحركات --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle gy-5">
                    <thead class="bg-light-primary">
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.entry_number')</th>
                            <th class="text-center min-w-100px">@lang('accusoft::models/as_reports.columns.date')</th>
                            <th class="text-center min-w-100px">@lang('accusoft::models/as_reports.columns.account_type')</th>
                            <th class="text-start  min-w-200px">@lang('accusoft::models/as_reports.columns.description')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_journal_entries.fields.created_by')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.debit')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.credit')</th>
                            <th class="text-center min-w-120px">@lang('accusoft::models/as_reports.columns.balance')</th>
                        </tr>
                    </thead>
                    @if (count($accountstat['transactions']) > 0)
                        <tr>
                            <th class="text-center">@lang('accusoft::models/as_reports.columns.previous_balance')</th>
                            <th colspan="4" class="text-center"></th>
                            <th class="text-center"> {{ $accountstat['opening_balance']['debit'] }}</th>
                            <th class="text-center"> {{ $accountstat['opening_balance']['credit'] }}</th>
                            <th class="text-center"> {{ $accountstat['opening_balance']['balance'] }}
                                ({{ $accountstat['opening_balance']['balance_type'] == 'debit' ? __('accusoft::models/as_tree_account.nature.debit') : __('accusoft::models/as_tree_account.nature.credit') }})
                            </th>
                        </tr>
                    @endif

                    <tbody>
                        @forelse ($accountstat['transactions'] as $transaction)
                            <tr>
                                <td class="text-center">
                                    @if (!empty($transaction['journal_entry_id']))
                                        <a href="{{ route('accusoft.JournalEntry.show', $transaction['journal_entry_id']) }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                                            {{ $transaction['entry_number'] }}
                                        </a>
                                    @else
                                        {{ $transaction['entry_number'] }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $transaction['date'] }}</td>
                                <td class="text-center">{{ $transaction['entry_type'] ?? '' }}</td>
                                <td class="text-start">
                                    @if (!empty($transaction['journal_entry_id']))
                                        <a href="{{ route('accusoft.JournalEntry.show', $transaction['journal_entry_id']) }}" target="_blank" class="text-dark text-hover-primary text-decoration-none">
                                            {{ $transaction['description'] }}
                                        </a>
                                    @else
                                        {{ $transaction['description'] }}
                                    @endif
                                    @if ($transaction['cost_center'])
                                        <br>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-building"></i>
                                            {{ $transaction['cost_center']['name'] }}
                                            ({{ $transaction['cost_center']['code'] }})
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-primary fw-semibold">{{ $transaction['created_by_user'] ?? '—' }}</span>
                                </td>
                                <td class="text-center {{ $transaction['debit'] > 0 ? 'text-success fw-bold' : '' }}">
                                    {{ $transaction['debit'] ?? '' }}
                                </td>
                                <td class="text-center {{ $transaction['credit'] > 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ $transaction['credit'] ?? '' }}
                                </td>
                                <td class="text-center">
                                    {{ $transaction['balance'] ?? '' }}
                                    <span
                                        class="badge badge-sm {{ $transaction['balance_type'] == 'debit' ? 'badge-light-success' : 'badge-light-danger' }}">
                                        {{ $transaction['balance_type'] == 'debit' ? __('accusoft::models/as_tree_account.nature.debit') : __('accusoft::models/as_tree_account.nature.credit') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-10">
                                    <i class="fa-solid fa-inbox fs-3x mb-3 d-block"></i>
                                    @lang('lang.no_data')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- صف الإجماليات --}}
                    @if (count($accountstat['transactions']) > 0)
                        <tfoot class="">
                            <tr class="fw-bold fs-6">
                                <th class="text-center">@lang('accusoft::models/as_reports.columns.net_balance')</th>
                                <th colspan="4" class="text-center "></th>
                                <th class="text-center ">{{ $accountstat['totals']['total_debit'] }}</th>
                                <th class="text-center ">{{ $accountstat['totals']['total_credit'] }}</th>
                                <th class="text-center">-</th>
                            </tr>
                            <tr class="fw-bold fs-6">
                                <th class="text-center">@lang('accusoft::models/as_reports.columns.closing_balance')</th>
                                <th colspan="4" class="text-center "></th>
                                <th class="text-center ">{{ $accountstat['closing_balance']['debit'] }}</th>
                                <th class="text-center">{{ $accountstat['closing_balance']['credit'] }}</th>
                                <th class="text-center"> {{ $accountstat['closing_balance']['balance'] }}
                                    ({{ $accountstat['closing_balance']['balance_type'] == 'debit' ? __('accusoft::models/as_tree_account.nature.debit') : __('accusoft::models/as_tree_account.nature.credit') }})
                                </th>
                            </tr>
                        </tfoot>
                    @endif

                </table>
            </div>



            {{-- معلومات التوليد --}}
            <div class="text-muted text-end mt-3">
                <small>
                    <i class="fa-solid fa-clock"></i>
                    @lang('accusoft::models/as_reports.columns.generated_at'): {{ $accountstat['generated_at'] }}
                </small>
            </div>
        </div>
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-body text-center py-20">
            <i  class="fa-solid fa-search fs-5x text-muted mb-5 d-block"></i>
            <h3 class="text-muted">@lang('accusoft::models/as_reports.messages.select_account')</h3>
            <p  class="text-muted">@lang('accusoft::models/as_reports.messages.select_account_description')</p>
        </div>
    </div>
@endif
