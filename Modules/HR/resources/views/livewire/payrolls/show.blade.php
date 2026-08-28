<div id="kt_app_content_container" class="app-container container-xxl">
    <!--begin::Navbar-->
    <div class="card mb-6 mb-xl-9">
        <div class="card-body pt-9 pb-0">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                <!--begin::Image-->
                {{-- <div
                    class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                    <img class="mw-50px mw-lg-75px" src="assets/media/svg/brand-logos/volicity-9.svg" alt="image" />
                </div> --}}
                <!--end::Image-->
                <!--begin::Wrapper-->
                <div class="flex-grow-1">
                    <!--begin::Head-->
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <!--begin::Details-->
                        <div class="d-flex flex-column">
                            <!--begin::Status-->
                            <div class="d-flex align-items-center mb-1">
                                <h4 class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">
                                    {{$payroll->payroll_date_text }}
                                </h4>
                                <span class="{{ $payroll->status_badge }} me-auto">
                                    {{ $payroll->status_text }}

                                </span>
                            </div>
                            <!--end::Status-->
                            <!--begin::Description-->
                            {{-- <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-500">
                                #1 Tool to get started with Web Apps any Kind & size
                            </div> --}}
                            <!--end::Description-->
                        </div>
                        <!--end::Details-->
                        <!--begin::Actions-->
                        <div class="d-flex mb-4">
                            @livewire('hr::payrolls.approvals.approve', ['payroll_id' => $payroll->id],
                            key('payrolls_approvals_approve'))
                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--end::Head-->
                    <!--begin::Info-->
                    <div class="d-flex flex-wrap justify-content-start">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">{{ $payroll->preparing_at_text }}</div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-500">
                                    @lang('hr::models/hr_payrolls.fields.preparing_at')
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">{{ $payroll->delivery_at_text }}</div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-500">
                                    @lang('hr::models/hr_payrolls.fields.delivery_at')
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">
                                        {{ $payroll->total_text }}
                                    </div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-500">
                                    @lang('hr::models/hr_payrolls.fields.total')
                                </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Details-->
            <div class="separator"></div>
            <!--begin::Nav-->
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <!--begin::Nav item-->
                <li class="nav-item" role="button">
                    <span class="nav-link text-active-primary py-5 me-6 {{$tab==='main' ? 'active' : '' }}"
                        wire:click="changeTab('main')">
                        @lang('hr::models/hr_payrolls.plural')
                    </span>
                </li>
                <!--end::Nav item-->
                <!--begin::Nav item-->
                <li class="nav-item" role="button">
                    <span class="nav-link text-active-primary py-5 me-6 {{$tab==='approvals' ? 'active' : '' }}"
                        wire:click="changeTab('approvals')">
                        @lang('hr::models/hr_payroll_approvals.plural')
                    </span>
                </li>
                <!--end::Nav item-->
                <!--begin::Nav item-->
                <li class="nav-item" role="button">
                    <span class="nav-link text-active-primary py-5 me-6 {{$tab==='employees' ? 'active' : '' }}"
                        wire:click="changeTab('employees')">
                        @lang('hr::models/hr_payroll_employees.plural')
                    </span>
                </li>
                <!--end::Nav item-->
                <!--begin::Nav item-->
                {{-- <li class="nav-item" role="button">
                    <span class="nav-link text-active-primary py-5 me-6 {{$tab==='logs' ? 'active' : '' }}"
                        wire:click="changeTab('logs')">
                        @lang('hr::models/hr_payrolls.fields.activity_Payroll')
                    </span>
                </li> --}}
                <!--end::Nav item-->
            </ul>
            <!--end::Nav-->
        </div>
    </div>
    <!--end::Navbar-->
    <!--begin::Row-->
    <div class="row gx-6 gx-xl-9">
        @switch($tab)
        @case('approvals')
        @livewire('hr::payrolls.approvals.index', [
        'payroll_id' => $payroll->id,
        'approvals_is_ready'=>$payroll->approvals_is_ready
        ], key('payrolls_approvals_index'))
        @break
        @case('employees')
        @livewire('hr::payrolls.employees.index', ['payroll_id' => $payroll->id], key('payrolls_employees_index'))
        @break
        @case('logs')
        @livewire('hr::payrolls.logs.index', ['payroll_id' => $payroll->id], key('payrolls_logs_index'))
        @break
        @default
        <div id="kt_app_content_container" class="container px-2">
            <div class="card mx-2">
                <div class="card-body">
                    <div class="row gap-1">
                        @include('hr::payrolls.show_fields')
                    </div>
                </div>
            </div>
        </div>
        @endswitch
    </div>
    <!--end::Row-->
</div>
