<div>
    
    <!-----in_progress'---->
    @switch($status)
    @case('ready')
    <span class="btn btn-sm btn-active-light-primary" wire:click='refreshPage()'>
        @lang('hr::models/hr_payrolls.ready_payroll_date',['date' => Carbon\Carbon::now()->format('Y-m')])
    </span>
    @break
  
    @case('open')
    <button type="button" class="btn btn-sm btn-primary" wire:click="toggleOpenModal()">
        @lang('hr::models/hr_payrolls.preparing_payroll_date',['date' => $payroll_date])
    </button>
    @if ($openModal)
    <div class="modal fade show" tabindex="-1" id="kt_modal_1" aria-modal="true" role="dialog"
        style="display: block;background: #18181b6b">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        @lang('hr::models/hr_payrolls.preparing_payroll_date',['date' => $payroll_date])
                    </h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-danger ms-2" wire:click="toggleOpenModal()">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Delivery At Field -->
                        <div class="form-group col-12 mb-5">
                            {!! Form::label('delivery_at', __('hr::models/hr_payrolls.fields.payroll_until') . ':') !!}
                            {!! Form::date('delivery_at', now()->format('Y-m-d'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-active-light-danger"
                        wire:click="toggleOpenModal()">@lang('crud.close')</button>
                    <button type="button" class="btn btn-sm btn-primary" wire:click="createPayroll()">
                        @lang('hr::models/hr_payrolls.preparing_payroll_date',['date' => $payroll_date])
                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                            wire:target="createPayroll">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @break
    @case('in_progress')
    <span class="btn btn-sm btn-active-light-danger" wire:poll.15s>
        @lang('hr::models/hr_payrolls.in_progress_payroll_date',['date' => $setting->payroll_date])
    </span>
    @break
    @default
    <button type="button" class="btn btn-sm btn-danger" disabled>
        @lang('hr::models/hr_payrolls.preparing_payroll_date',['date' => $payroll_date])
    </button>
    @endswitch
</div>
