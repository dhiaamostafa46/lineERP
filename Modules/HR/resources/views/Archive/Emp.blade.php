{{-- Employee Details     $user->photo_original_path ?? --}}
<div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
    <!-- Begin: Card -->
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-15">
            <!-- Begin: Summary -->
            <div class="d-flex flex-center flex-column mb-5">
                <!-- Avatar -->
                <div class="symbol symbol-100px symbol-circle mb-7">
                    <img src="{{ $Employee->main_employee->UserTrashed->photo_original_path ?? asset('admin_assets/media/avatars/300-3.jpg') }}" class="rounded-3" alt="Employee Avatar">
                </div>
                <!-- Name -->
                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">
                    {{ $Employee->main_employee->full_name ?? __('N/A') }}
                </a>



                
                <!-- Position -->
                <div class="fs-5 fw-semibold text-muted mb-6">
                    {{ $Employee->job->name ?? __('N/A') }}
                </div>



                <!-- Info -->
                {{-- <div class="d-flex flex-wrap flex-center">
                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                        <div class="fs-4 fw-bold text-gray-700">
                            <span>{{ number_format($Employee->earnings ?? 0) }}</span>
                            <i class="ki-duotone ki-arrow-up fs-3 text-success"></i>
                        </div>
                        <div class="fw-semibold text-muted">@lang('Earnings')</div>
                    </div>
                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
                        <div class="fs-4 fw-bold text-gray-700">
                            <span>{{ number_format($Employee->tasks ?? 0) }}</span>
                            <i class="ki-duotone ki-arrow-down fs-3 text-danger"></i>
                        </div>
                        <div class="fw-semibold text-muted">@lang('Tasks')</div>
                    </div>
                    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                        <div class="fs-4 fw-bold text-gray-700">
                            <span>{{ number_format($Employee->hours ?? 0) }}</span>
                            <i class="ki-duotone ki-arrow-up fs-3 text-success"></i>
                        </div>
                        <div class="fw-semibold text-muted">@lang('Hours')</div>
                    </div>
                </div> --}}
            </div>
            <div class="separator separator-dashed my-3"></div>
            <!-- Begin: Details -->
            <div id="kt_customer_view_details" class="collapse show">
                <div class="py-5 fs-6">
                    <div class="fw-bold mt-5">@lang('models/employees.fields.username')</div>
                    <div class="text-gray-600">{{ $Employee->username ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">@lang('models/employees.fields.email')</div>
                    <div class="text-gray-600">
                        <a href="mailto:{{ $Employee->main_employee->email ?? '' }}" class="text-hover-primary">
                            {{ $Employee->main_employee->email ?? __('N/A') }}
                        </a>
                    </div>

                    <div class="fw-bold mt-5">@lang('models/employees.fields.phone')</div>
                    <div class="text-gray-600">
                        <a href="tel:{{ $Employee->main_employee->phone ?? '' }}" class="text-hover-primary">
                            {{ $Employee->main_employee->phone ?? __('N/A') }}
                        </a>
                    </div>

                    <div class="fw-bold mt-5">@lang('models/employees.fields.address')</div>
                    <div class="text-gray-600">
                        {{ $Employee->main_employee->address ?? __('N/A') }}
                        <br>{{ $Employee->main_employee->national_address ?? '' }}
                    </div>

                    <div class="fw-bold mt-5">@lang('models/employees.fields.dob')</div>
                    <div class="text-gray-600">{{ $Employee->main_employee->dob ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">@lang('models/employees.fields.religion')</div>
                    <div class="text-gray-600">{{ $Employee->main_employee->religion ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">@lang('models/employees.fields.marital_status')</div>
                    <div class="text-gray-600">{{ $Employee->main_employee->marital_status_text ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">  @lang('hr::models/hr_employees.fields.start_at')</div>
                    <div class="text-gray-600">{{ $Employee->start_at ?? __('N/A') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-15">

            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">
                @lang('models/employees.fields.bank_details')
            </a>
            <div class="separator separator-dashed my-3"></div>
            <!-- Begin: Details -->
            <div id="kt_customer_view_details" class="collapse show">
                <div class="py-5 fs-6">
                    <div class="fw-bold mt-5">@lang('models/employees.fields.bank_details')</div>
                    <div class="text-gray-600">{{ $Employee->main_employee->bank->bank_name  ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">  @lang('models/employees.fields.iban') </div>
                    <div class="text-gray-600">{{ $Employee->main_employee->bank->iban ?? __('N/A') }}</div>

                </div>
            </div>
        </div>
    </div>

    <div class="card mb-5 mb-xl-8">
        <div class="card-body pt-15">

            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">
                @lang('models/employees.fields.identity_details')
            </a>
            <div class="separator separator-dashed my-3"></div>
            <!-- Begin: Details -->
            <div id="kt_customer_view_details" class="collapse show">
                <div class="py-5 fs-6">
                    <div class="fw-bold mt-5">@lang('models/employees.fields.identity_type')</div>
                    <div class="text-gray-600">{{ $Employee->main_employee->identity->identity_type  ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">  @lang('models/employees.fields.identity_no') </div>
                    <div class="text-gray-600">{{ $Employee->main_employee->identity->identity_no ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">  @lang('models/employees.fields.identity_expired_at') </div>
                    <div class="text-gray-600">{{ $Employee->main_employee->identity->identity_expired_at ?? __('N/A') }}</div>

                    <div class="fw-bold mt-5">  @lang('models/employees.fields.insurance_no') </div>
                    <div class="text-gray-600">{{ $Employee->main_employee->identity->insurance_no ?? __('N/A') }}</div>


                    <div class="fw-bold mt-5">  @lang('models/employees.fields.insurance_expired_at') </div>
                    <div class="text-gray-600">{{ $Employee->main_employee->identity->insurance_expired_at ?? __('N/A') }}</div>

                </div>
            </div>
        </div>
    </div>
    <!-- End: Card -->
</div>
