@extends('layouts.app')

@section('title', __('hr::lang.profile'))

@section('content')


    @if (auth()->user()->employee)


        <div class="d-flex flex-wrap flex-sm-nowrap">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <img src="/admin_assets/media/avatars/blank.png" alt="image" />
                    <div
                        class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px">
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <a href="#"
                                class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $employee->main_employee->full_name }}</a>
                            <a href="#">
                                <i class="ki-duotone ki-verify fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                        <!--end::Name-->
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>{{ $employee->job->name }}</a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-duotone ki-geolocation fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>{{ $employee->main_employee->address }}</a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                <i class="ki-duotone ki-sms fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>{{ $employee->main_employee->email }}</a>
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->

                </div>
                <!--end::Title-->
                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-arrow-up fs-3 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i> <!--suffix-->
                                    <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $employee->salary->basic }}" data-kt-countup-prefix="SAR">
                                        0
                                    </div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-500">@lang('hr::models/hr_salaries.fields.basic')</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-arrow-down fs-3 text-danger me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $employee->salary->totalDeduct() }}">0</div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-500">@lang('hr::models/hr_salaries.fields.total_deduct')</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-arrow-up fs-3 text-success me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-2 fw-bold" data-kt-countup="true"
                                        data-kt-countup-value="{{ $employee->salary->totalAllowance() }}">0</div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-500">@lang('hr::models/hr_salaries.fields.total_allowance')</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Wrapper-->
                    <!--begin::Progress-->
                    <!--<div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">-->
                    <!--    <div class="d-flex justify-content-between w-100 mt-auto mb-2">-->
                    <!--        <span class="fw-semibold fs-6 text-gray-500">Profile Compleation</span>-->
                    <!--        <span class="fw-bold fs-6">50%</span>-->
                    <!--    </div>-->
                    <!--    <div class="h-5px mx-3 w-100 bg-light mb-3">-->
                    <!--        <div class="bg-success rounded h-5px" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--end::Progress-->
                </div>
                <!--end::Stats-->
            </div>
        </div>











































        <!---------------------------------------------------Tabs header ----------------------------------------------------------------------------------------------------->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 active" data-bs-toggle="tab"
                    href="#overview_tab">@lang('hr::models/hr_dashboard.overview')
                </a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#justifications_tab">@lang('hr::models/hr_dashboard.settlement')</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#holiday_tab">@lang('hr::models/hr_dashboard.holiday')</a>
            </li>

            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#advance_tab">@lang('hr::models/hr_dashboard.loans')</a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#penalties_tab">@lang('hr::models/hr_penalties.plural')</a>
            </li>
            <!--end::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#absent_tab">@lang('hr::models/hr_dashboard.fields.leavePermission')</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab" href="#custodies_tab">
                    @lang('hr::models/hr_custodies.plural') </a>
            </li>
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#task_tab">@lang('hr::models/hr_dashboard.tasks')</a>
            </li>
            <!--begin::Nav item-->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" data-bs-toggle="tab"
                    href="#docs_tab">مستنداتي</a>
            </li>
            <!--end::Nav item-->
            <!--begin::Nav item-->

        </ul>
        <div class="tab-content" id="myTabContent">
            <!---------------------------------------------OverView Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade show active" id="overview_tab" role="tabpanel">
                <div class="row">
                    <div class="col-xl-4">
                        <!--begin::List Widget 6-->
                        <div class="card card-xl-stretch mb-5 mb-xl-8">

                            <div class="card-body pt-0">
                                <!--begin::Item-->
                                <div class="d-flex align-items-center bg-light-success rounded p-5 mb-7">
                                    <i class="ki-duotone ki-abstract-26 text-success fs-1 me-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Title-->
                                    <div class="flex-grow-1 me-2">
                                        <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">المهام</a>
                                        {{-- <span class="text-muted fw-semibold d-block">Due in 2 Days</span> --}}
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Lable-->
                                    <span class="fw-bold text-success py-1">{{ count($tasks) }}</span>
                                    <!--end::Lable-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-7">
                                    <i class="ki-duotone ki-abstract-26 text-warning fs-1 me-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Title-->
                                    <div class="flex-grow-1 me-2">
                                        <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">طلبات
                                            الإجازة</a>
                                        {{-- <span class="text-muted fw-semibold d-block">Due in 2 Days</span> --}}
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Lable-->
                                    <span class="fw-bold text-warning py-1">{{ count($holidays) }}</span>
                                    <!--end::Lable-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex align-items-center bg-light-danger rounded p-5 mb-7">
                                    <i class="ki-duotone ki-abstract-26 text-danger fs-1 me-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Title-->
                                    <div class="flex-grow-1 me-2">
                                        <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">طلبات
                                            السلفيات</a>
                                        {{-- <span class="text-muted fw-semibold d-block">Due in 5 Days</span> --}}
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Lable-->
                                    <span class="fw-bold text-danger py-1">{{ count($advances) }}</span>
                                    <!--end::Lable-->
                                </div>
                                <!--end::Item-->
                                <div class="d-flex align-items-center bg-light-warning rounded p-5 mb-7">
                                    <i class="ki-duotone ki-abstract-26 text-warning fs-1 me-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <!--begin::Title-->
                                    <div class="flex-grow-1 me-2">
                                        <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-6">
                                            @lang('hr::models/hr_dashboard.fields.leavePermission')
                                        </a>
                                        {{-- <span class="text-muted fw-semibold d-block">Due in 2 Days</span> --}}
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Lable-->
                                    <span class="fw-bold text-active py-1">{{ count($absentrequests) }}</span>
                                    <!--end::Lable-->
                                </div>

                            </div>
                            <!--end::Body-->
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <!--begin::List Widget 6-->
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0">
                                <h3 class="card-title fw-bold text-gray-900">@lang('hr::models/hr_dashboard.peronalInfo') </h3>
                            </div>
                            <div class="card-body pt-0">
                                <div class="pb-5 fs-6">

                                    {{-- <div class="text-gray-600">ID-45453423</div> --}}
                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.birthdate') : {{ $employee->main_employee->dob }}
                                    </div>

                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.phone'): {{ $employee->main_employee->phone }}
                                    </div>

                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.email'): {{ $employee->main_employee->email }}
                                    </div>

                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.address') : {{ $employee->main_employee->address }}
                                    </div>

                                    <!--begin::Details item-->
                                    <div class="fw-bold mt-5"> @lang('hr::models/hr_dashboard.fields.martialStatus') :
                                        {{ $employee->main_employee->getMaritalStatusTextAttribute() }}</div>
                                    {{-- <div class="fw-bold mt-5"> <a href="{{ route('hr.empdashboard.employessSalary') }}"
                                            type="button" class="btn btn-primary text-hover-primary d-flex flex-column"
                                            target="_blank"> تعريف بالراتب </a></div> --}}



                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <!--begin::List Widget 6-->
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0">
                                <h3 class="card-title fw-bold text-gray-900">@lang('hr::models/hr_dashboard.job_contract')</h3>
                            </div>
                            <div class="card-body pt-0">
                                <!--begin::Details item-->
                                <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.empNo') : {{ $employee->job_number }}</div>
                                <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.job_id') : {{ $employee->job->name ?? '' }}</div>
                                <div class="fw-bold mt-5">@lang('hr::models/hr_dashboard.fields.department_id') : {{ $employee->department->name ?? '' }}
                                </div>
                                <div class="fw-bold mt-5"> @lang('hr::models/hr_dashboard.fields.start_at') : {{ $employee->start_at ?? '' }}</div>
                                <div class="fw-bold mt-5"> @lang('hr::models/hr_dashboard.fields.contractExp'): {{ $contract->end_at ?? '' }}</div>
                                {{--
                                <div class="fw-bold mt-5">الراتب الاساسي : {{ $employee->salary->basic ?? '' }}</div>
                                <div class="fw-bold mt-5">البدلات : {{ $employee->salary->totalAllowance() ?? '' }}</div>
                                <div class="fw-bold mt-5">الخصومات : {{ $employee->salary->totalDeduct() ?? '' }}</div>
                                <div class="fw-bold mt-5">المكافآت : {{ $employee->totalAdvances() ?? '' }}</div>
                                <div class="fw-bold mt-5">الجزاءات : {{ $employee->totalPenalties() ?? '' }}</div> --}}


                                {{-- <div class="fw-bold mt-5"> <a href="{{ $contract->file_original_path ?? '' }}"
                                        type="button" target="_blank"
                                        class="btn btn-primary text-hover-primary d-flex flex-column">تحميل
                                        العقد</a></div>
                                <div class="fw-bold mt-5"> <a href="{{ $contract->file_original_path ?? '' }}"
                                        type="button" target="_blank"
                                        class="btn btn-primary text-hover-primary d-flex flex-column">كشف الراتب </a></div> --}}
                            </div>
                        </div>
                    </div>

                    <div class="row g-6 g-xl-9">
                        <!--begin::List Widget 6-->
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0">
                                <h3 class="card-title fw-bold text-gray-900">الحضور والإنصراف</h3>
                            </div>
                            <div class="card-body pt-0">
                                @if (count($Places) > 0)
                                    @include('hr::Attendance.card')
                                @else
                                    <h4 style="color: brown">لم يتم تحديد مكان الحضور والإنصراف يرجى التواصل مع إدارة
                                        الموارد
                                        البشرية</h4>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------------Justification Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="justifications_tab" role="tabpanel">
                <div class="card-body p-0">

                    <a href="{{ route('hr.empdashboard.justificationsEmployee') }}"
                        class="btn btn-sm btn-primary float-left">@lang('crud.create')</a>
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-justifications-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.request_date')</th>
                                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.reason')</th>
                                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.shift_id')</th>
                                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.type')</th>
                                    <th class="min-w-200px">@lang('hr::models/hr_justifications.fields.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($justifications as $justification)
                                    <tr>
                                        <td>{{ $justification->request_date->format('Y-m-d') }}</td>
                                        <td>{{ $justification->reason }}</td>
                                        <td>
                                            @php
                                                $shift = optional($justification->HrShift);
                                            @endphp

                                            @if ($shift->from && $shift->to)
                                                <div
                                                    style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                    <div style="display: flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                        <strong>{{ \Carbon\Carbon::parse($shift->from)->format('h:i A') }}</strong>
                                                    </div>

                                                    <span style="font-weight: bold;">-</span>

                                                    <div style="display: flex; align-items: center; gap: 4px;">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                        <strong>{{ \Carbon\Carbon::parse($shift->to)->format('h:i A') }}</strong>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td><span>{{ $justification->type_text }}</span></td>
                                        <td><span
                                                class="{{ $justification->status_badge }}">{{ $justification->status_text }}</span>
                                        </td>
                                        <td>
                                            @if ($justification->attachment_url)
                                                <a href="{{ $justification->attachment_url }}" target="_blank">
                                                    عرض المرفق
                                                </a>
                                            @else
                                                لا يوجد
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4">
                        <div class="float-right">
                            @include('adminlte-templates::common.paginate', ['records' => $justifications])
                        </div>
                    </div>
                </div>
            </div>

            <!---------------------------------------------Holiday Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="holiday_tab" role="tabpanel">
                <div class="card-body p-0">
                    <a href="{{ route('hr.my-requests.create') }}"
                        class="btn btn-sm btn-primary float-left">@lang('crud.create')</a>
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-holidays-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th>@lang('hr::models/hr_holidays.fields.type_id')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($holidays as $holiday)
                                    <tr>
                                        <td>{{ $holiday->type->name ?? '' }}</td>
                                        <td>{{ $holiday->from_at->format('Y-m-d h:i a') }}</td>
                                        <td>{{ $holiday->end_at->format('Y-m-d h:i a') }}</td>
                                        <td><span class="{{ $holiday->status_badge }}">{{ $holiday->status_text }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4">
                        <div class="float-right">
                            @include('adminlte-templates::common.paginate', ['records' => $holidays])
                        </div>
                    </div>
                </div>

            </div>
            <!---------------------------------------------Documents Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="docs_tab" role="tabpanel">
                <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
                    @foreach ($documents as $doc)
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <!--begin::Card-->
                            <div class="card h-100">
                                <!--begin::Card body-->
                                <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                    <!--begin::Name-->
                                    <a href="{{ $doc->file_original_path }}" target="_blank"
                                        class="text-gray-800 text-hover-primary d-flex flex-column">
                                        <!--begin::Image-->
                                        <div class="symbol symbol-60px mb-5">
                                            <!-- blank-image--->
                                            <img src="{{ asset('admin_assets/media/svg/files/pdf.svg') }}"
                                                class="theme-light-show" alt="" />
                                            <img src="{{ asset('admin_assets/media/svg/files/pdf-dark.svg') }}"
                                                class="theme-dark-show" alt="" />
                                        </div>
                                        <!--end::Image-->
                                        <!--begin::Title-->
                                        <div class="fs-5 fw-bold mb-2">{{ $doc->type->name }}</div>
                                        <!--end::Title-->
                                    </a>
                                    <!--end::Name-->

                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                    @endforeach

                </div>
            </div>
            <!---------------------------------------------Advance Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="advance_tab" role="tabpanel">
                <div class="card-body p-0">
                    <a type="button" class="btn btn-sm btn-primary float-left" data-bs-toggle="modal"
                        data-bs-target="#NewAdvance">@lang('crud.create')</a>
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-advances-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th>@lang('hr::models/hr_advances.fields.amount')</th>
                                    <th>@lang('hr::models/hr_advances.fields.due_at')</th>
                                    <th>@lang('hr::models/hr_advances.fields.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($advances as $advance)
                                    <tr>
                                        <td>{{ $advance->amount }}</td>
                                        <td>{{ $advance->due_at }}</td>
                                        <td><span class="{{ $advance->status_badge }}">{{ $advance->status_text }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4">
                        <div class="float-right">
                            @include('adminlte-templates::common.paginate', ['records' => $advances])
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------------Penalties Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="penalties_tab" role="tabpanel">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-penalties-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th>@lang('hr::models/hr_penalties.fields.description')</th>
                                    <th>@lang('hr::models/hr_penalties.fields.amount')</th>
                                    <th>@lang('hr::models/hr_penalties.fields.due_date')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penalties as $penalty)
                                    <tr>
                                        <td>{{ $penalty->description }}</td>
                                        <td>{{ $penalty->amount }}</td>
                                        <td>{{ $penalty->due_date_text }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4">
                        <div class="float-right">
                            @include('adminlte-templates::common.paginate', ['records' => $penalties])
                        </div>
                    </div>
                </div>
            </div>
            <!---------------------------------------------custodies Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="custodies_tab" role="tabpanel">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-custodies-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">

                                    <th>@lang('hr::models/hr_custodies.fields.asset_id')</th>
                                    <th>@lang('hr::models/hr_custodies.fields.details')</th>

                                    <th>@lang('hr::models/hr_custodies.fields.received_at')</th>
                                    <th>@lang('hr::models/hr_custodies.fields.status')</th>
                                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($custodies as $custody)
                                    <tr>

                                        <td>{{ $custody->asset->name ?? '' }}</td>
                                        <td>{{ $custody->details ?? '' }}</td>
                                        <td>{{ $custody->received_at ?? '' }}</td>
                                        <td>
                                            <span class="{{ $custody->status_badge }}">
                                                {{ $custody->status_text }}
                                            </span>
                                        </td>
                                        <td style="width: 120px">
                                            @if ($custody->status == 1)
                                                <a href="{{ route('hr.custodies.receive', [$custody->id]) }}"
                                                    class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                                    <i class="fa-regular fa-square-check"></i>
                                                </a>
                                            @elseif ($custody->status == 2)
                                                <a href="{{ route('hr.custodies.Return', [$custody->id]) }}"
                                                    class='btn btn-icon btn-sm btn-light-primary btn-xs'>

                                                    <i class="fa-sharp fa-solid fa-rotate-right"></i>
                                                </a>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
            <!---------------------------------------------Taks Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="task_tab" role="tabpanel">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-advances-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th>@lang('hr::models/hr_tasks.details.hr_task_id')</th>
                                    <th>@lang('hr::models/hr_tasks.details.created_at')</th>
                                    <th>@lang('hr::models/hr_tasks.fields.title')</th>
                                    <th>@lang('hr::models/hr_tasks.fields.status')</th>
                                    <th>@lang('hr::models/hr_tasks.fields.done')</th>
                                    {{-- <th colspan="3" class="text-center">@lang('crud.action')</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td>{{ $task->id }}</td>
                                        <td>{{ $task->created_at }}</td>
                                        <td>{{ $task->title }}</td>
                                        <td><span class="{{ $task->status_badge }}">{{ $task->status_text }}</span></td>
                                        <td>{{ $task->done }}</td>
                                        <td style="width: 120px">
                                            <a href="{{ route('hr.Task.showTask', [$task->id]) }}"
                                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!---------------------------------------------Absence Tab--------------------------------------------------------------------------------------------------------->
            <div class="tab-pane fade" id="absent_tab" role="tabpanel">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <a type="button" class="btn btn-sm btn-primary float-left" data-bs-toggle="modal"
                            data-bs-target="#NewAbsent">@lang('crud.create')</a>

                        <table class="table table-striped gy-7 gs-7" id="hr-holidays-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    {{-- <th>@lang('hr::models/hr_holidays.fields.employee_id')</th> --}}
                                    <th>@lang('hr::models/hr_absentrequest.fields.requestdate')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.details')</th>
                                    <th>@lang('hr::models/hr_holidays.fields.status')</th>
                                    {{-- <th colspan="3" class="text-center">@lang('crud.action')</th> --}}

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($absentrequests as $absent)
                                    <tr>
                                        {{-- <td>{{ $absent->employee->username ?? '' }}</td> --}}
                                        {{-- <td>
                                @livewire('hr::trackers.get-status', ['model' => $absent], key('trackers_get_status_'.$absent->id))

                            </td> --}}
                                        <td>{{ $absent->request_date->format('Y-m-d') ?? '' }}</td>
                                        <td>{{ $absent->from_at }}</td>
                                        <td>{{ $absent->end_at }}</td>
                                        <td>{{ $absent->details ?? '' }}</td>
                                        <td> <span class="{{ $absent->status_badge }}">{{ $absent->status_text }}</span>
                                        </td>


                                        {{-- <td style="width: 120px">
                                {!! Form::open(['route' => ['hr.holidays.destroy', $absent->id], 'method' => 'delete']) !!}
                                <div class='btn-group'>
                                    <a href="{{ route('hr.holidays.show', [$absent->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('hr.holidays.edit', [$absent->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                    'onclick' => "return confirm('Are you sure?')",
                                    ]) !!}
                                </div>
                                {!! Form::close() !!}
                            </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4">
                        <div class="float-right">
                            @include('adminlte-templates::common.paginate', ['records' => $absentrequests])
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--------------------------------------------------------Modals------------------------------------------------------------>
        <!--begin::Modal - New Advance-->
        <?php $employee_id = auth()->user()->employee->id; ?>
        <div class="modal fade" id="NewAdvance" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content rounded">
                    <!--begin::Modal header-->
                    <div class="modal-header pb-0 border-0 justify-content-end">
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--begin::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                        <!--begin:Form-->

                        <!--begin::Heading-->
                        <div class="mb-13 text-center">
                            <!--begin::Title-->
                            <h1 class="mb-3">@lang('hr::models/hr_advances.fields.add')</h1>
                            <!--end::Title-->
                            <!--begin::Description-->
                            <div class="text-muted fw-semibold fs-5">@lang('hr::models/hr_advances.fields.addtitle')
                            </div>
                            <!--end::Description-->
                        </div>
                        <!--end::Heading-->
                        @include('hr::my_requests.advances_create_form')
                        <!--end:Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - New Target-->
        <!--begin::Modal - New Advance-->
        <?php $employee_id = auth()->user()->employee->id; ?>
        <div class="modal fade" id="NewAbsent" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content rounded">
                    <!--begin::Modal header-->
                    <div class="modal-header pb-0 border-0 justify-content-end">
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--begin::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                        <!--begin:Form-->
                        {!! Form::open(['route' => 'hr.absentrequests.store', 'files' => true]) !!}
                        <!--begin::Heading-->
                        <div class="mb-13 text-center">
                            <!--begin::Title-->
                            <h1 class="mb-3">@lang('hr::models/hr_absentrequest.fields.addorder')</h1>
                            <!--end::Title-->
                            <!--begin::Description-->
                            <div class="text-muted fw-semibold fs-5">@lang('hr::models/hr_absentrequest.fields.addtitle')
                            </div>
                            <!--end::Description-->
                        </div>
                        <!--end::Heading-->
                        @include('hr::absentrequests.fields')

                        {!! Form::close() !!}
                        <!--end:Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
    @else
        <p>عفوا ليس لديك صلاحية الوصول لهذه الصفحة يرجى التواصل مع الإدارة </p>
    @endif
    <!--end::Modal - New Target-->
@endsection
