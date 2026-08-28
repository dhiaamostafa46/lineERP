<div class="card">
    <div class="card-body">
        <!-- ============================== -->
        <!-- Payroll Settings Section -->
        <!-- ============================== -->
        <div class="mb-10">
            <h3 class="card-title mb-5">
                <i class="ki-duotone ki-wallet fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                @lang('hr::models/hr_settings.sections.payroll_settings')
            </h3>
            <div class="row">
                <!-- Delivery Payroll At Field -->
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-calendar fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {!! Form::label('delivery_payroll_at', __('hr::models/hr_settings.fields.delivery_payroll_at'), ['class' => 'fw-bold mb-0']) !!}
                            </div>
                            {!! Form::number('delivery_payroll_at', null, ['class' => 'form-control', 'placeholder' => __('hr::models/hr_settings.placeholders.enter_day'), 'min' => '1', 'max' => '31']) !!}
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.delivery_payroll_hint')</small>
                        </div>
                    </div>
                </div>

                <!-- Preparing Payroll At Field -->
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-timer fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {!! Form::label('preparing_payroll_at', __('hr::models/hr_settings.fields.preparing_payroll_at'), ['class' => 'fw-bold mb-0']) !!}
                            </div>
                            {!! Form::number('preparing_payroll_at', null, ['class' => 'form-control', 'placeholder' => __('hr::models/hr_settings.placeholders.enter_day'), 'min' => '1', 'max' => '31']) !!}
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.preparing_payroll_hint')</small>
                        </div>
                    </div>
                </div>

                <!-- Min Salary Field -->
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-dollar fs-2 text-warning me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {!! Form::label('min_salary', __('hr::models/hr_settings.fields.min_salary'), ['class' => 'fw-bold mb-0']) !!}
                            </div>
                            {!! Form::number('min_salary', null, ['class' => 'form-control', 'step' => '0.01', 'placeholder' => __('hr::models/hr_settings.placeholders.enter_amount')]) !!}
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.min_salary_hint')</small>
                        </div>
                    </div>
                </div>

                <!-- Currency Field -->
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-abstract-26 fs-2 text-info me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {!! Form::label('currency', __('hr::models/hr_settings.fields.currency'), ['class' => 'fw-bold mb-0']) !!}
                            </div>
                            {!! Form::select('currency', ['SAR' => 'SAR - ريال سعودي', 'USD' => 'USD - دولار أمريكي', 'EUR' => 'EUR - يورو', 'AED' => 'AED - درهم إماراتي'], 'SAR', ['class' => 'form-control']) !!}
                            <small class="text-muted d-block mt-2">العملة الأساسية للنظام</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Approval Workflow -->
            <div class="separator separator-dashed my-6"></div>
            <h5 class="mb-4">
                <i class="ki-duotone ki-shield-tick fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                @lang('hr::models/hr_settings.fields.approval_payroll')
            </h5>
            <div id="approval_payroll">
                <div class="form-group">
                    <div data-repeater-list="approval_payroll">
                        @forelse ($setting->approval_payroll??[] as $item)
                        <div data-repeater-item class="mb-3">
                            <div class="card card-bordered shadow-sm">
                                <div class="card-body p-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            {!! Form::label('approval_payroll[user_id]', __('hr::models/hr_settings.fields.user_id') . ':') !!}
                                            {!! Form::select('approval_payroll[user_id]', $users, $item['user_id']??null, ['class' => 'form-control', 'placeholder' => __('hr::models/hr_settings.placeholders.select_user')]) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::label('approval_payroll[sort]', __('hr::models/hr_settings.fields.sort') . ':') !!}
                                            {!! Form::number('approval_payroll[sort]', $item['sort']??null, ['class' => 'form-control', 'placeholder' => '1']) !!}
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch form-check-custom form-check-solid mt-2 mt-md-7">
                                                <input class="form-check-input is-current-check" type="checkbox" value="1" name="approval_payroll[is_current]" {{ isset($item['is_current']) ? 'checked' : '' }}/>
                                                <label class="form-check-label text-dark fw-bold">
                                                    @lang('hr::models/hr_settings.fields.is_current')
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-icon btn-light-danger mt-2 mt-md-7">
                                                <i class="ki-duotone ki-trash fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                </i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div data-repeater-item class="mb-3">
                            <div class="card card-bordered shadow-sm">
                                <div class="card-body p-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            {!! Form::label('approval_payroll[user_id]', __('hr::models/hr_settings.fields.user_id') . ':') !!}
                                            {!! Form::select('approval_payroll[user_id]', $users, null, ['class' => 'form-control', 'placeholder' => __('hr::models/hr_settings.placeholders.select_user')]) !!}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::label('approval_payroll[sort]', __('hr::models/hr_settings.fields.sort') . ':') !!}
                                            {!! Form::number('approval_payroll[sort]', null, ['class' => 'form-control', 'placeholder' => '1']) !!}
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch form-check-custom form-check-solid mt-2 mt-md-7">
                                                <input class="form-check-input is-current-check" type="checkbox" value="1" name="approval_payroll[is_current]" />
                                                <label class="form-check-label text-dark fw-bold">
                                                    @lang('hr::models/hr_settings.fields.is_current')
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <a href="javascript:;" data-repeater-delete class="btn btn-sm btn-icon btn-light-danger mt-2 mt-md-7">
                                                <i class="ki-duotone ki-trash fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                </i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    <div class="form-group mt-3">
                        <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                            <i class="ki-duotone ki-plus fs-3"></i>
                            @lang('hr::models/hr_settings.buttons.add_approver')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================== -->
        <!-- Attendance Settings Section -->
        <!-- ============================== -->
        <div class="mb-10">
            <h3 class="card-title mb-5">
                <i class="ki-duotone ki-calendar-tick fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                @lang('hr::models/hr_settings.sections.attendance_settings')
            </h3>
            <div class="row">
                <!-- Calculate Missing Fingerprint Field -->
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card card-bordered h-100 border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-fingerprint-scanning fs-2 text-primary me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <span class="fw-bold">@lang('hr::models/hr_settings.fields.calculate_missing_fingerprint')</span>
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                {!! Form::hidden('calculate_missing_fingerprint', 0) !!}
                                {!! Form::checkbox('calculate_missing_fingerprint', 1, null, ['class' => 'form-check-input', 'id' => 'calculate_missing_fingerprint']) !!}
                                {!! Form::label('calculate_missing_fingerprint', 'تفعيل احتساب البصمة الناقصة', ['class' => 'form-check-label text-dark']) !!}
                            </div>
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.calculate_fingerprint_hint')</small>
                        </div>
                    </div>
                </div>

                <!-- Missing Fingerprint Policy Field -->
                <div class="col-sm-6 col-lg-4 mb-4" id="missing_fingerprint_policy_wrapper">
                    <div class="card card-bordered h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-information fs-2 text-danger me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {!! Form::label('missing_fingerprint_policy', __('hr::models/hr_settings.fields.missing_fingerprint_policy'), ['class' => 'fw-bold mb-0']) !!}
                            </div>
                            {!! Form::select('missing_fingerprint_policy', $missingFingerprintPolicies, null, ['class' => 'form-control', 'placeholder' => __('hr::models/hr_settings.placeholders.select_policy')]) !!}
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.missing_fingerprint_hint')</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================== -->
        <!-- Leave Settings Section -->
        <!-- ============================== -->
        <div class="mb-10">
            <h3 class="card-title mb-5">
                <i class="ki-duotone ki-shield-search fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                @lang('hr::models/hr_settings.sections.leave_settings')
            </h3>
            <div class="row">
                <!-- Max Off Days Field -->
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card card-bordered h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-calendar-8 fs-2 text-success me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                                {!! Form::label('max_off_days', __('hr::models/hr_settings.fields.max_off_days'), ['class' => 'fw-bold mb-0']) !!}
                            </div>
                            {!! Form::number('max_off_days', null, ['class' => 'form-control', 'placeholder' => __('hr::models/hr_settings.placeholders.enter_days')]) !!}
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.max_off_days_hint')</small>
                        </div>
                    </div>
                </div>

                <!-- Leave Include Weekend Field -->
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card card-bordered h-100 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-calendar-add fs-2 text-info me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                                <span class="fw-bold">@lang('hr::models/hr_settings.fields.leave_include_weekend')</span>
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                {!! Form::hidden('leave_include_weekend', 0) !!}
                                {!! Form::checkbox('leave_include_weekend', 1, null, ['class' => 'form-check-input', 'id' => 'leave_include_weekend']) !!}
                                {!! Form::label('leave_include_weekend', 'احتساب عطلة نهاية الأسبوع', ['class' => 'form-check-label text-dark']) !!}
                            </div>
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.leave_weekend_hint')</small>
                        </div>
                    </div>
                </div>

                <!-- Leave Include Holidays Field -->
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card card-bordered h-100 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ki-duotone ki-flag fs-2 text-warning me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <span class="fw-bold">@lang('hr::models/hr_settings.fields.leave_include_holidays')</span>
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                {!! Form::hidden('leave_include_holidays', 0) !!}
                                {!! Form::checkbox('leave_include_holidays', 1, null, ['class' => 'form-check-input', 'id' => 'leave_include_holidays']) !!}
                                {!! Form::label('leave_include_holidays', 'احتساب العطلات الرسمية', ['class' => 'form-check-label text-dark']) !!}
                            </div>
                            <small class="text-muted d-block mt-2">@lang('hr::models/hr_settings.hints.leave_holidays_hint')</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin_assets') }}/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script>
    $(document).ready(function() {
        // Toggle Missing Fingerprint Policy visibility
        function toggleMissingFingerprintPolicy() {
            if ($('#calculate_missing_fingerprint').is(':checked')) {
                $('#missing_fingerprint_policy_wrapper').slideDown(300);
            } else {
                $('#missing_fingerprint_policy_wrapper').slideUp(300);
            }
        }

        $('#calculate_missing_fingerprint').on('change', toggleMissingFingerprintPolicy);
        toggleMissingFingerprintPolicy();

        // Exclusive check for is_current in repeater
        $(document).on('change', '.is-current-check', function() {
            if ($(this).is(':checked')) {
                $('.is-current-check').not(this).prop('checked', false);
            } else if ($('.is-current-check:checked').length === 0) {
                $(this).prop('checked', true);
            }
        });

        // Ensure at least one is checked on load
        if ($('.is-current-check').length > 0 && $('.is-current-check:checked').length === 0) {
            $('.is-current-check').first().prop('checked', true);
        }

        // Initialize repeater
        $('#approval_payroll').repeater({
            initEmpty: false,
            defaultValues: {
                'text-input': 'foo'
            },
            show: function () {
                $(this).slideDown(300);
                var $checkbox = $(this).find('.is-current-check');
                if ($('.is-current-check').length === 1) {
                    $checkbox.prop('checked', true);
                } else {
                    $checkbox.prop('checked', false);
                }
            },
            hide: function (deleteElement) {
                if(confirm('@lang("hr::models/hr_settings.messages.confirm_delete")')) {
                    $(this).slideUp(300, deleteElement);
                }
            }
        });
    });
</script>
@endpush
