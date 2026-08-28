<div class="card card-flush h-xl-100">
    <!--begin::Heading-->
    <div class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-200px" style="background: linear-gradient(to right, #1cfcc8, #01a07b);" data-bs-theme="light">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column text-white pt-15">
            <span class="fw-bold fs-2x mb-3">@lang('hr::lang.employee_requests')</span>
        </h3>
    </div>
    <!--end::Heading-->
    <!--begin::Body-->
    <div class="card-body mt-n15">
        <!--begin::Stats-->
        <div class="mt-n15 position-relative">
            <!--begin::Row-->
            <div class="row g-3 g-lg-6">
                <!--begin::Col-->
                <div class="col-6 col-md-4">
                    <!--begin::Items-->
                    <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                        <!--begin::Symbol-->
                        <div class="symbol symbol-30px me-5 mb-8">
                            <span class="symbol-label">
                                <i class="ki-duotone ki-flask fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <!--end::Symbol-->
                        <!--begin::Stats-->
                        <div class="m-0">
                            <!--begin::Number-->
                            <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $counts->justification }} </span>
                            <!--end::Number-->
                            <!--begin::Desc-->
                            <span class="text-gray-500 fw-semibold fs-6">@lang('hr::models/hr_justifications.plural') </span>
                            <!--end::Desc-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Col-->

                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-6 col-md-4">
                    <!--begin::Items-->
                    <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                        <!--begin::Symbol-->
                        <div class="symbol symbol-30px me-5 mb-8">
                            <span class="symbol-label">
                                <i class="ki-duotone ki-award fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                        </div>
                        <!--end::Symbol-->
                        <!--begin::Stats-->
                        <div class="m-0">
                            <!--begin::Number-->
                            <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $counts->advances }}</span>
                            <!--end::Number-->
                            <!--begin::Desc-->
                            <span class="text-gray-500 fw-semibold fs-6">@lang('hr::models/hr_advances.plural')</span>
                            <!--end::Desc-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-6 col-md-4">
                    <!--begin::Items-->
                    <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5 h-100">
                        <!--begin::Symbol-->
                        <div class="symbol symbol-30px me-5 mb-8">
                            <span class="symbol-label">
                                <i class="ki-duotone ki-timer fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                        </div>
                        <!--end::Symbol-->
                        <!--begin::Stats-->
                        <div class="m-0">
                            <!--begin::Number-->
                            <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $counts->holidays }}</span>
                            <!--end::Number-->
                            <!--begin::Desc-->
                            <span class="text-gray-500 fw-semibold fs-6">@lang('hr::models/hr_holidays.plural')</span>
                            <!--end::Desc-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Stats-->
    </div>
    <!--end::Body-->
</div>
