<!--begin::Tabs-->
<div class="card card-flush shadow-sm border-0 mb-5 mb-xl-8">
    <div class="card-header pt-5">
        <div class="card-title">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab" href="#kt_tab_depreciation">
                        <i class="fa-solid fa-chart-area me-2"></i> @lang('accusoft::models/as_asset.depreciation_history')
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#kt_tab_transactions">
                        <i class="fa-solid fa-list-check me-2"></i> @lang('accusoft::models/as_asset.transactions_history')
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="myTabContent">
            <!-- Depreciation Tab -->
            <div class="tab-pane fade show active" id="kt_tab_depreciation" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-7 gy-4">
                        <thead>
                            <tr class="fw-bold fs-6 text-gray-500 bg-light">
                                <th class="ps-4 rounded-start">@lang('accusoft::models/as_asset.period')</th>
                                <th class="text-end">@lang('accusoft::models/as_asset.depreciation_amount')</th>
                                <th class="text-end">@lang('accusoft::models/as_asset.accumulated_depreciation')</th>
                                <th class="text-end">@lang('accusoft::models/as_asset.fields.current_book_value')</th>
                                <th class="text-end pe-4 rounded-end">@lang('accusoft::models/as_asset.journal_entry')</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($asset->depreciations->sortBy([['year', 'asc'], ['month', 'asc']]) as $dep)
                                <tr class="{{ !$dep->is_posted ? 'bg-light-warning' : '' }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-30px me-3">
                                                <span class="symbol-label bg-light-primary text-primary fw-bold">{{ str_pad($dep->month, 2, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold">{{ $dep->year }}</span>
                                                @if(!$dep->is_posted)
                                                    <span class="badge badge-light-warning fs-8 px-2 py-1 mt-1">{{ __('accusoft::models/as_asset.scheduled') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-danger fw-bold">{{ number_format($dep->depreciation_amount, 2) }}</td>
                                    <td class="text-end text-gray-800 fw-bold">{{ number_format($dep->accumulated_depreciation, 2) }}</td>
                                    <td class="text-end text-success fw-bold">{{ number_format($dep->book_value, 2) }}</td>
                                    <td class="text-end pe-4">
                                        @if($dep->is_posted && $dep->journal_entry_id)
                                            <a href="{{ route('accusoft.JournalEntry.show', $dep->journal_entry_id) }}" class="btn btn-sm btn-light btn-active-light-primary">
                                                <i class="fa-solid fa-link fs-7"></i> @lang('accusoft::models/as_asset.view_journal')
                                            </a>
                                        @elseif(!$dep->is_posted)
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#execute_depreciation_modal_{{ $dep->id }}">
                                                <i class="fa-solid fa-play fs-7"></i> {{ __('accusoft::models/as_asset.execute') }}
                                            </button>
                                            
                                            <!-- Execute Modal -->
                                            <div class="modal fade" tabindex="-1" id="execute_depreciation_modal_{{ $dep->id }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content text-start">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('accusoft::models/as_asset.execute_depreciation') }}</h5>
                                                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                                                                <i class="fa-solid fa-xmark fs-2x"></i>
                                                            </div>
                                                        </div>
                                                        <form action="{{ route('accusoft.assets.execute_depreciation', ['asset' => $asset->id, 'depreciation' => $dep->id]) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-5">
                                                                    <label class="form-label required">{{ __('accusoft::models/as_asset.execution_date') }}</label>
                                                                    <input type="date" name="execution_date" class="form-control form-control-solid" value="{{ $dep->period_date ? \Carbon\Carbon::parse($dep->period_date)->format('Y-m-d') : '' }}" required>
                                                                </div>
                                                                <div class="alert alert-warning d-flex align-items-center p-5 mb-0">
                                                                    <i class="fa-solid fa-triangle-exclamation fs-2hx text-warning me-4"></i>
                                                                    <div class="d-flex flex-column">
                                                                        <h4 class="mb-1 text-warning">{{ __('accusoft::models/as_asset.accounting_alert') }}</h4>
                                                                        <span>{{ __('accusoft::models/as_asset.journal_entry_will_be_created_with_amount') }} {{ number_format($dep->depreciation_amount, 2) }} {{ __('lang.local_currency') }}. {{ __('accusoft::models/as_asset.are_you_sure_to_continue') }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('crud.cancel')</button>
                                                                <button type="submit" class="btn btn-primary">{{ __('accusoft::models/as_asset.confirm_and_execute') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-10 fs-6">@lang('accusoft::models/as_asset.no_depreciation_history')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="tab-pane fade" id="kt_tab_transactions" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-7 gy-4">
                        <thead>
                            <tr class="fw-bold fs-6 text-gray-500 bg-light">
                                <th class="ps-4 rounded-start">@lang('accusoft::models/as_asset.transaction_date')</th>
                                <th class="text-center">@lang('accusoft::models/as_asset.transaction_type')</th>
                                <th class="text-end">@lang('accusoft::models/as_asset.amount')</th>
                                <th class="text-start">@lang('accusoft::models/as_asset.notes')</th>
                                <th class="text-end pe-4 rounded-end">@lang('accusoft::models/as_asset.journal_entry')</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($asset->transactions ?? [] as $transaction)
                                <tr>
                                    <td class="ps-4">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        @php
                                            $types = \Modules\AccuSoft\App\Models\AssetTransaction::getTransactionTypes();
                                        @endphp
                                        @if($transaction->transaction_type == \Modules\AccuSoft\App\Models\AssetTransaction::TYPE_PURCHASE) 
                                            <span class="badge badge-light-primary fw-bold">{{ $types[$transaction->transaction_type] ?? __('accusoft::models/as_asset.transactions.purchase') }}</span>
                                        @elseif($transaction->transaction_type == \Modules\AccuSoft\App\Models\AssetTransaction::TYPE_DEPRECIATION) 
                                            <span class="badge badge-light-info fw-bold">{{ $types[$transaction->transaction_type] ?? __('accusoft::models/as_asset.transactions.depreciation') }}</span>
                                        @elseif($transaction->transaction_type == \Modules\AccuSoft\App\Models\AssetTransaction::TYPE_DISPOSAL) 
                                            <span class="badge badge-light-danger fw-bold">{{ $types[$transaction->transaction_type] ?? __('accusoft::models/as_asset.transactions.disposal') }}</span>
                                        @else 
                                            <span class="badge badge-light-secondary fw-bold">{{ $types[$transaction->transaction_type] ?? $transaction->transaction_type }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-gray-800 fw-bold">{{ number_format($transaction->amount, 2) }}</td>
                                    <td class="text-start text-muted">{{ $transaction->notes }}</td>
                                    <td class="text-end pe-4">
                                        @if($transaction->journal_entry_id)
                                            <a href="{{ route('accusoft.JournalEntry.show', $transaction->journal_entry_id) }}" class="btn btn-sm btn-light btn-active-light-primary">
                                                <i class="fa-solid fa-link fs-7"></i> @lang('accusoft::models/as_asset.view_journal')
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-10 fs-6">@lang('accusoft::models/as_asset.no_transactions_history')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Transactions Tab -->
        </div>
    </div>
</div>
<!--end::Tabs-->
