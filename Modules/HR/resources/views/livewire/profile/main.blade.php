<div>
        @include('hr::livewire.profile.partials.posts_feed')

      <div class="card shadow-sm mb-7">
            <div class="card-body">
                <div class="row g-5 align-items-center">
                    <!-- Profile Avatar & Name -->
                    <div class="col-12 col-md-4 col-lg-3">
                        <div class="text-center">
                            <div class="symbol symbol-150px symbol-circle mb-5 mx-auto position-relative hover-scale-110" style="transition: transform 0.3s ease;">
                                <img src="{{ $employee->main_employee->photo_url ?? '/admin_assets/media/avatars/blank.png' }}"
                                    alt="@lang('hr::models/hr_dashboard.fields.employee_photo')" class="border border-4 border-white shadow-lg" />
                                <div
                                    class="position-absolute translate-middle bottom-0 start-100 mb-8 bg-success rounded-circle border border-4 border-white h-25px w-25px pulse-animation">
                                </div>
                            </div>
                            <style>
                                .hover-scale-110:hover {
                                    transform: scale(1.1);
                                }
                                .pulse-animation {
                                    animation: pulse 2s infinite;
                                }
                                @keyframes pulse {
                                    0% {
                                        box-shadow: 0 0 0 0 rgba(50, 205, 50, 0.4);
                                    }
                                    70% {
                                        box-shadow: 0 0 0 10px rgba(50, 205, 50, 0);
                                    }
                                    100% {
                                        box-shadow: 0 0 0 0 rgba(50, 205, 50, 0);
                                    }
                                }
                            </style>
                            <h2 class="fs-2 fw-bold text-gray-900 mb-2">{{ $employee->full_name ?? '' }}</h2>
                            <div class="badge badge-light-primary fs-6 fw-semibold mb-3">
                                {{ optional($employee->branch)->name ?? '' }}
                            </div>
                            <div class="d-flex flex-column gap-2 text-muted fs-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="ki-duotone ki-geolocation fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ $employee->address ?? '' }}
                                </div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="ki-duotone ki-sms fs-3 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ $employee->email ?? '' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Statistics -->
                    <div class="col-12 col-md-8 col-lg-9">
                        <div class="row g-4">
                            <!-- Basic Salary -->
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="d-flex align-items-center bg-light-success rounded p-5 h-100">
                                    <i class="ki-duotone ki-wallet text-success fs-3x me-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    <div class="flex-grow-1">
                                        <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $employee->hrEmployee->salary->basic }}" data-kt-countup-prefix="SAR">
                                        0
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Total Deductions -->
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="d-flex align-items-center bg-light-danger rounded p-5 h-100">
                                    <i class="ki-duotone ki-arrow-down-left text-danger fs-3x me-5"><span class="path1"></span><span class="path2"></span></i>
                                    <div class="flex-grow-1">
                                         <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $employee->hrEmployee->salary->totalDeduct() }}">0</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Total Allowances -->
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="d-flex align-items-center bg-light-primary rounded p-5 h-100">
                                    <i class="ki-duotone ki-arrow-up-right text-primary fs-3x me-5"><span class="path1"></span><span class="path2"></span></i>
                                    <div class="flex-grow-1">
                                        <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $employee->hrEmployee->salary->totalAllowance() }}">0</div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Cards Section -->
        <div class="row g-6 g-xl-7 mb-7">
            <!-- Personal Information Card -->
            <div class="col-12 col-lg-6 col-xl-4">
                <div class="card shadow-sm h-100 hover-elevate-up" style="transition: all 0.3s ease;">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800 fs-3">@lang('hr::models/hr_dashboard.peronalInfo')</span>

                        </h3>
                    </div>
                    <div class="card-body pt-3">
                        <div class="d-flex flex-column gap-4">
                            <!-- Birth Date -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-primary w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.birthdate')</span>
                                    </div>
                                    <span class="text-gray-600 fs-6">{{ $employee->dob }}</span>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-info w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.phone')</span>
                                    </div>
                                    <span class="text-gray-600 fs-6">{{ $employee->phone }}</span>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-success w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.email')</span>
                                    </div>
                                    <span class="text-gray-600 fs-6">{{ $employee->email }}</span>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-warning w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.address')</span>
                                    </div>
                                    <span class="text-gray-600 fs-6">{{ $employee->address }}</span>
                                </div>
                            </div>

                            <!-- Marital Status -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-danger w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.martialStatus')</span>
                                    </div>
                                    <span
                                        class="text-gray-600 fs-6">{{ $employee->getMaritalStatusTextAttribute() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job & Contract Information Card -->
            <div class="col-12 col-lg-6 col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800 fs-3">@lang('hr::models/hr_dashboard.job_contract')</span>
                        </h3>
                    </div>
                    <div class="card-body pt-3">
                        <div class="d-flex flex-column gap-4">
                            <!-- Employee Number -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-primary w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.empNo')</span>
                                    </div>
                                    <span  class="text-gray-600 fs-6">{{ optional($employee->hrEmployee)->job_number ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Job Title -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-info w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.job_id')</span>
                                    </div>
                                    <span
                                        class="text-gray-600 fs-6">{{ optional(optional($employee->hrEmployee)->job)->name ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Department -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-success w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.department_id')</span>
                                    </div>
                                    <span
                                        class="text-gray-600 fs-6">{{ optional(optional($employee->hrEmployee)->department)->name ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-warning w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.start_at')</span>
                                    </div>
                                    <span
                                        class="text-gray-600 fs-6">{{ optional($employee->hrEmployee)->start_at ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Contract Expiry -->
                            <div class="d-flex align-items-center position-relative">
                                <div class="position-absolute top-0 start-0 rounded h-100 bg-danger w-4px"></div>
                                <div class="ms-5 w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-gray-800 fs-6">@lang('hr::models/hr_dashboard.fields.contractExp')</span>
                                    </div>
                                    <span class="text-gray-600 fs-6"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card (Optional Third Column) -->
            <div class="col-12 col-lg-12 col-xl-4">
                <div class="card shadow-sm h-100 bg-gradient-primary">
                    <div class="card-body d-flex flex-column justify-content-center text-center p-8">
                        <i class="ki-duotone ki-user-tick fs-5x text-white mb-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <h3 class="text-white fw-bold fs-2 mb-3">ملف الموظف</h3>
                        <p class="text-white opacity-75 fs-6 mb-0">
                            جميع معلومات وبيانات الموظف في مكان واحد
                        </p>
                        <div class="separator separator-dashed border-white opacity-25 my-5"></div>
                        <div class="d-flex justify-content-around">
                            <div>
                                <i class="ki-duotone ki-shield-tick fs-3x text-white mb-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-white fs-6 fw-semibold">موثق</div>
                            </div>
                            <div>
                                <i class="ki-duotone ki-verify fs-3x text-white mb-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-white fs-6 fw-semibold">معتمد</div>
                            </div>
                            <div>
                                <i class="ki-duotone ki-lock fs-3x text-white mb-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-white fs-6 fw-semibold">آمن</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
