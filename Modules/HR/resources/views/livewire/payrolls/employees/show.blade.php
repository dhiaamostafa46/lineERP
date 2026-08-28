<div>
    @if ($openModal)
    <div class="modal fade show" tabindex="-1" id="kt_modal_scrollable_1" aria-modal="true" role="dialog"
        style="display: block; padding-left: 0px;background: #18181b6b;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">

                    <div class="modal-title">
                        <h5>{{ $employee->username }}</h5>
                        <p class="text-muted">
                            {{ $employee->job_name }}
                            {{ '@'. $employee->department_name }}
                        </p>
                    </div>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" wire:click="closeModal()">
                        <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div class="table-responsive scrollable">
                        <table class="table  table-striped gy-7 gs-7" id="hr-payroll-transactions-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th>@lang('hr::models/hr_payroll_transactions.fields.type')</th>
                                    <th>@lang('hr::models/hr_payroll_transactions.fields.amount')</th>
                                    <th>@lang('hr::models/hr_payroll_transactions.fields.status')</th>
                                    <th>@lang('hr::models/hr_payroll_transactions.fields.note')</th>
                                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->type_text }} -({{$transaction->name  }} )</td>
                                    <td>
                                        <b
                                            class="{{ $transaction->is_deduct ? 'text-danger' : 'text-success' }}">
                                            {{ $transaction->amount }}
                                            {{ $transaction->currency }}
                                        </b>
                                    </td>
                                    <td>
                                        <span class="{{ $transaction->status_badge }}">
                                            {{ $transaction->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($hr_setting->payroll_id)
                                        <textarea wire:model.live="note" id="" cols="30" rows="2"></textarea>
                                        <button class="btn btn-sm btn-icon btn-light-primary"
                                            wire:click='updateNote({{ $transaction->id }})'>
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        @endif
                                        <p class="text-muted">{{ $transaction->note }}</p>
                                    </td>
                                    <td style="width: 120px">
                                        @if ($hr_setting->payroll_id)
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-icon btn-light-primary"
                                                wire:click="approve({{ $transaction->id }})">
                                                <i class="fa-solid fa-check"></i>
                                                <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                                    wire:target="approve">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-light-danger"
                                                wire:click="reject({{ $transaction->id }})">
                                                <i class="fa-solid fa-x"></i>
                                                <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                                    wire:target="reject">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light"
                    wire:click="closeModal()">@lang('crud.close')</button>
                    @if ($hr_setting->payroll_id)
                    <button class="btn btn-sm btn-light-primary" wire:click="approveAll()">
                        <i class="fa-solid fa-check"></i> @lang('crud.all')
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                            wire:target="approveAll">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                    <button class="btn btn-sm btn-light-danger" wire:click="rejectAll()">
                        <i class="fa-solid fa-x"></i> @lang('crud.all')
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                            wire:target="rejectAll">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
