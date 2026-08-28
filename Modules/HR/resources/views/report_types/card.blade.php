


<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
    <!--begin::Col-->
    <div class="col">
        <!--begin::Card-->
        <div class="card card-flush h-md-100">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2> @lang('hr::models/hr_report_types.additional_reports.holidays') </h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-1">
                <!--begin::Users-->

                <!--end::Users-->
                <!--begin::Permissions-->
                <div class="d-flex flex-column text-gray-600">
                    {{-- <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.LeaveHolday') }}"> @lang('hr::models/hr_report_types.LeaveHolday') </a>
                    </div> --}}
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.LeaveHoldaybalance') }}"> @lang('hr::models/hr_report_types.LeaveHoldaybalance') </a>
                    </div>
                </div>


                <!--end::Permissions-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col">
        <!--begin::Card-->
        <div class="card card-flush h-md-100">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2> @lang('hr::models/hr_report_types.additional_reports.financial_reports') </h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-1">
                <!--begin::Users-->

                <!--end::Users-->

                <!--begin::Permissions-->
                <div class="d-flex flex-column text-gray-600">
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.rewards') }}"> @lang('hr::models/hr_report_types.rewards') </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.EndService') }}"> @lang('hr::models/hr_report_types.EndService') </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>

                        <a href="{{ route('hr.Report.Payroll') }}"> @lang('hr::models/hr_report_types.Payroll') </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.advances') }}"> @lang('hr::models/hr_report_types.advances') </a>
                    </div>
                </div>
                <!--end::Permissions-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col">
        <!--begin::Card-->
        <div class="card card-flush h-md-100">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2>  @lang('hr::models/hr_report_types.additional_reports.attendance_reports')  </h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-1">
                <!--begin::Users-->

                <!--end::Users-->

                <!--begin::Permissions-->
                <div class="d-flex flex-column text-gray-600">
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.Attendance') }}"> @lang('hr::models/hr_report_types.Attendance') </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.SummaryAttendance') }}">
                            @lang('hr::models/hr_report_types.SummaryAttendance')
                        </a>

                    </div>


                     <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.Fingerprint') }}"> @lang('hr::models/hr_report_types.Fingerprint') </a>
                    </div>
{{-- 
                     <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.DeductionAttendance') }}"> @lang('hr::models/hr_report_types.DeductionAttendance') </a>
                    </div> --}}


                </div>
                <!--end::Permissions-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->

    <!--begin::Col-->
    <div class="col">
        <!--begin::Card-->
        <div class="card card-flush h-md-100">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2> @lang('hr::models/hr_report_types.additional_reports.employee_reports')  </h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-1">
                <!--begin::Users-->

                <!--end::Users-->

                <!--begin::Permissions-->
                <div class="d-flex flex-column text-gray-600">
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.Departments') }}"> @lang('hr::models/hr_report_types.Departments')  </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.Expired_Identity') }}"> @lang('hr::models/hr_report_types.Expired_identity') </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.Contact') }}"> @lang('hr::models/hr_report_types.Contact')  </a>
                    </div>
                    <div class="d-flex align-items-center py-2">
                        <span class="bullet bg-primary me-3"></span>
                        <a href="{{ route('hr.Report.custodies') }}"> @lang('hr::models/hr_report_types.custodies')  </a>
                    </div>
                </div>
                <!--end::Permissions-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Col-->
</div>
