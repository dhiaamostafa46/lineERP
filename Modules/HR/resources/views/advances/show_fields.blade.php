<div class="container-fluid">
    <div class="row">


        <!-- Main Information Card -->
        <div class="col-xl-12">
            <div class="row g-5 mb-10">
                <div class="col-md-6">
                    <div class="d-flex align-items-center bg-light-primary rounded p-5 h-100">
                        <i class="ki-duotone ki-profile-user fs-3x text-primary me-5"><span class="path1"></span><span
                                class="path2"></span><span class="path3"></span></i>
                        <div class="flex-grow-1">
                            <div class="text-gray-600 fw-semibold fs-7">@lang('hr::models/hr_advances.fields.employee_id')</div>
                            <div class="text-gray-800 fw-bold fs-4">{{ $advance->employee->username ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center bg-light-success rounded p-5 h-100">
                        <i class="ki-duotone ki-dollar fs-3x text-success me-5"><span class="path1"></span><span
                                class="path2"></span></i>
                        <div class="flex-grow-1">
                            <div class="text-gray-600 fw-semibold fs-7">@lang('hr::models/hr_advances.fields.amount')</div>
                            <div class="text-gray-800 fw-bold fs-4">{{ number_format($advance->amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="d-flex flex-stack mb-5">
                        <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_advances.fields.from_date')</div>
                        <div class="text-gray-800 fw-bold fs-6">
                            {{ $advance->from_date ? \Carbon\Carbon::parse($advance->from_date)->format('Y-m-d') : 'N/A' }}
                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-stack mb-5">
                        <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_advances.fields.to_date')</div>
                        <div class="text-gray-800 fw-bold fs-6">
                            {{ $advance->to_date ? \Carbon\Carbon::parse($advance->to_date)->format('Y-m-d') : 'N/A' }}
                        </div>
                    </div>
                    <div class="separator separator-dashed"></div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex flex-stack my-5">
                        <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_advances.fields.status')</div>
                        <div class="text-gray-800 fw-bold fs-6">@livewire('hr::trackers.get-status', ['model' => $advance], key('trackers_get_status'))</div>
                    </div>
                    <div class="separator separator-dashed"></div>
                </div>
                @if ($advance->approver)
                    <div class="col-md-6">
                        <div class="d-flex flex-stack my-5">
                            <div class="text-gray-600 fw-semibold fs-6">@lang('hr::lang.approver')</div>
                            <div class="text-gray-800 fw-bold fs-6">{{ $advance->approver->name ?? 'N/A' }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                    </div>
                @endif
                @if ($advance->attachment)
                    <div class="col-md-6">
                        <div class="d-flex flex-stack my-5">
                            <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_advances.fields.attachment')</div>
                            <div class="text-gray-800 fw-bold fs-6">
                                <a href="{{ asset($advance->attachment_path) }}" target="_blank"
                                    class="btn btn-light-primary btn-sm">
                                    <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    {{ $advance->attachment }}
                                </a>
                            </div>
                        </div>
                        <div class="separator separator-dashed"></div>
                    </div>
                @endif
            </div>

            <!-- Reason & Description -->
            <div class="mt-10">
                @if ($advance->reason)
                    <div class="mb-5">
                        <h5 class="text-gray-700">@lang('hr::models/hr_advances.fields.reason')</h5>
                        <div class="text-gray-600 p-4 bg-light rounded">{{ $advance->reason }}</div>
                    </div>
                @endif
                @if ($advance->description)
                    <div class="mb-5">
                        <h5 class="text-gray-700">@lang('hr::models/hr_advances.fields.description')</h5>
                        <div class="text-gray-600 p-4 bg-light rounded">{{ $advance->description }}</div>
                    </div>
                @endif
            </div>

            <!-- Timestamps -->
            <div class="d-flex justify-content-end text-muted fs-7 mt-10">
                <span class="me-5">@lang('hr::models/hr_advances.fields.created_at'):
                    {{ $advance->created_at ? $advance->created_at->format('Y-m-d H:i') : 'N/A' }}</span>
                <span>@lang('hr::models/hr_advances.fields.updated_at'):
                    {{ $advance->updated_at ? $advance->updated_at->format('Y-m-d H:i') : 'N/A' }}</span>
            </div>
        </div>

        <!-- Monthly Payments Card -->
        <div class="col-xl-12">

            @if ($advance->monthlyPayments && $advance->monthlyPayments->count() > 0)
                <div class="table-responsive">
                    <table class="table text-center table-bordered table-row-bordered table-hover gs-7">
                        <thead class="bg-light">
                            <tr class="fw-bold fs-6 text-gray-800">
                                <th class="fw-bold">@lang('hr::models/hr_monthly_payments.fields.due_at')</th>
                                <th class="fw-bold ">@lang('hr::models/hr_monthly_payments.fields.amount')</th>
                                <th class="fw-bold ">@lang('hr::models/hr_monthly_payments.fields.status')</th>
                                <th class="fw-bold ">@lang('hr::models/hr_monthly_payments.fields.approver_id')</th>
                                    <th class="fw-bold ">@lang('hr::models/hr_advances.fields.attachment')</th>
                                <th class="fw-bold">@lang('hr::models/hr_monthly_payments.fields.type')</th>
                                <th class="fw-bold">@lang('crud.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPaid = 0;
                                $totalAmount =
                                    $advance->amount > 0 ? $advance->amount : $advance->monthlyPayments->sum('amount');
                            @endphp
                            @foreach ($advance->monthlyPayments as $payment)
                                @php
                                    if ($payment->status == \Modules\HR\App\Models\HrMonthlyPayment::STATUS_APPROVED) {
                                        $totalPaid += $payment->amount;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            {{ $payment->due_at->format('Y-m') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light-primary fs-6">
                                            {{ number_format($payment->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $payment->status_badge }}">
                                            {{ $payment->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            {{ $payment->approver->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                         <div class="text-gray-800 fw-bold fs-6">
                                <a href="{{ asset($payment->attachment_path) }}" target="_blank"
                                    class="btn btn-light-primary btn-sm">
                                    <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    {{ $payment->attachment }}
                                </a>
                            </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $payment->types_badge }}">
                                            {{ $payment->types_text }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if (
                                            $payment->status == \Modules\HR\App\Models\HrMonthlyPayment::STATUS_APPROVED &&
                                                $payment->type == \Modules\HR\App\Models\HrMonthlyPayment::TYPE_PENDING)
                                            <button type="button" class="btn btn-sm btn-light-warning"
                                                data-bs-toggle="modal" data-bs-target="#delayPaymentModal"
                                                data-payment-id="{{ $payment->id }}" data-type="delay"
                                                data-due-at="{{ $payment->due_at->format('Y-m') }}">
                                                @lang('hr::lang.delay')
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-danger"
                                                data-bs-toggle="modal" data-bs-target="#RepaidPaymentModal"
                                                data-payment-id="{{ $payment->id }}" data-type="Repaid"
                                                data-due-at="{{ $payment->due_at->format('Y-m') }}">
                                                @lang('hr::lang.Repaid')
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th class="fw-bolder">@lang('hr::models/hr_monthly_payments.fields.total')</th>
                                <th class="text-end fw-bolder fs-6">
                                    {{ number_format($advance->monthlyPayments->sum('amount'), 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <i class="ki-duotone ki-information-5 fs-4x text-muted mb-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <p class="text-muted fs-6">@lang('hr::models/hr_monthly_payments.fields.no_monthly_payments')</p>
                </div>
            @endif
        </div>

        {{-- Employee Advance Details --}}
        @if (isset($balanceDetails))
            <div class="col-xl-12 mt-10">
                <div class="card card-flush h-md-100">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>@lang('hr::models/hr_monthly_payments.fields.employee_advance_details')</h2>
                        </div>
                    </div>
                    <div class="card-body pt-1">
                        <div class="d-flex flex-stack my-5">
                            <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_employees.fields.max_advance')</div>
                            <div class="text-gray-800 fw-bold fs-6">{{ $advance->employee->max_advance ?? '0' }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack my-5">
                            <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_monthly_payments.fields.total_advances')</div>
                            <div class="text-gray-800 fw-bold fs-6">
                                {{ number_format($balanceDetails['total_approved'] ?? 0, 2) }}</div>
                        </div>
                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack my-5">
                            <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_monthly_payments.fields.total_paid')</div>
                            <div class="text-gray-800 fw-bold fs-6">
                                {{ number_format($balanceDetails['total_paid'] ?? 0, 2) }}</div>
                        </div>


                        <div class="separator separator-dashed"></div>
                        <div class="d-flex flex-stack my-5">
                            <div class="text-gray-600 fw-semibold fs-6">@lang('hr::models/hr_monthly_payments.fields.remaining_balance')</div>
                            <div class="text-gray-800 fw-bold fs-6">
                                {{ number_format($balanceDetails['balance'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif



    </div>
</div>
<style>
    .table-row-bordered tbody tr {
        transition: all 0.2s ease;
    }

    .table-row-bordered tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 1s ease-in-out;
    }
</style>



<div class="modal fade" id="delayPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delayPaymentModalLabel"> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!! Form::open(['route' => 'hr.advances.update_payment', 'method' => 'post']) !!}
            <div class="modal-body">
                <input type="hidden" name="payment_id" id="delay_payment_id">
                <input type="hidden" name="type_class" id="delay_type_class" value="delay">
                <div class="mb-3">
                    <label for="delay_new_due_date" class="form-label">@lang('hr::lang.new_due_date')</label>
                    <input type="month" class="form-control" id="delay_new_due_date" name="new_due_date" required>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('crud.cancel')</button>
                <button type="submit" class="btn btn-primary">@lang('crud.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<!-- Delay Payment Modal -->
<div class="modal fade" id="RepaidPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repaidPaymentModalLabel"> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {!! Form::open(['route' => 'hr.advances.update_payment', 'method' => 'post', 'files' => true]) !!}
            <div class="modal-body">
                <input type="hidden" name="payment_id" id="repaid_payment_id">
                <input type="hidden" name="type_class" id="repaid_type_class"  value="Repaid">
                <div class="mb-3">
                    <label for="repaid_new_due_date" class="form-label">@lang('hr::lang.new_due_date')</label>
                    <input type="month" class="form-control" id="repaid_new_due_date" name="new_due_date">
                </div>

                <div class="mb-3">
                    <label for="repaid_attachment" class="form-label">@lang('hr::models/hr_advances.fields.attachment')</label>
                    <input type="file" class="form-control" id="repaid_attachment" name="attachment">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('crud.cancel')</button>
                <button type="submit" class="btn btn-primary">@lang('crud.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var delayPaymentModal = document.getElementById('delayPaymentModal');
            var RepaidPaymentModal = document.getElementById('RepaidPaymentModal');

            if (delayPaymentModal) {
                delayPaymentModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var paymentId = button.getAttribute('data-payment-id');
                    var type_class = button.getAttribute('data-type');
                    var dueAt = button.getAttribute('data-due-at');

                    var modal = this;
                    modal.querySelector('#delay_payment_id').value = paymentId;
                    modal.querySelector('#delay_type_class').value = type_class;
                    modal.querySelector('#delayPaymentModalLabel').innerHTML = type_class;
                    var newDueDateInput = modal.querySelector('#delay_new_due_date');
                    var nextMonth = getNextMonth(dueAt);
                    newDueDateInput.min = nextMonth;
                    newDueDateInput.value = nextMonth; // تعبئة التاريخ تلقائياً
                });
            }

            if (RepaidPaymentModal) {
                RepaidPaymentModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var paymentId = button.getAttribute('data-payment-id');
                    var type_class = button.getAttribute('data-type');
                    var dueAt = button.getAttribute('data-due-at');

                    var modal = this;
                    modal.querySelector('#repaid_payment_id').value = paymentId;
                    modal.querySelector('#repaid_type_class').value = type_class;
                    modal.querySelector('#repaidPaymentModalLabel').innerHTML = type_class;
                    var newDueDateInput = modal.querySelector('#repaid_new_due_date');
                    newDueDateInput.min = getNextMonth(dueAt);
                    // تعبئة التاريخ تلقائياً بتاريخ الاستحقاق
                    newDueDateInput.value = dueAt; 
                });

            }

            function getNextMonth(dateString) {
                var parts = dateString.split('-');
                var year = parseInt(parts[0], 10);
                var month = parseInt(parts[1], 10);
                month++;
                if (month > 12) {
                    month = 1;
                    year++;
                }
                return year + '-' + month.toString().padStart(2, '0');
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        #delayPaymentModal .modal-body {
            padding: 2rem;
        }

        #delay_new_due_date, #repaid_new_due_date {
            direction: ltr;
            text-align: right;
        }
    </style>
@endpush
