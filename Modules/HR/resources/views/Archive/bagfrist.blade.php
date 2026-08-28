

<div class="card h-100 mb-6 mb-xl-9">
    <div class="card-body p-9">

        <div class="fs-2hx fw-bold">

            @lang('hr::models/hr_salaries.fields.total') @lang('hr::models/hr_salaries.singular') </div>
        <div class="separator separator-dashed my-3"></div>
        <div class="fs-6 d-flex justify-content-between mb-4">
            <div class="fw-semibold"> @lang('hr::models/hr_salaries.fields.basic')</div>
            <div class="d-flex fw-bold">
                <i class="ki-duotone ki-arrow-up-right fs-3 me-1 text-success">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i> {{ $salary->basic }}
            </div>
        </div>
        <div class="separator separator-dashed my-3"></div>
        @foreach ($salary->salary_allowances as $salary_allowance)
            <div class="fs-6 d-flex justify-content-between mb-4">
                <div class="fw-semibold"> {{ $salary_allowance->allowance->name }} </div>
                <div class="d-flex fw-bold">
                    <i class="ki-duotone ki-arrow-up-right fs-3 me-1 text-success">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i> {{ $salary_allowance->amount }}
                </div>
            </div>
            <div class="separator separator-dashed"></div>
        @endforeach


        @foreach ($salary->salary_deducts as $salary_deduct)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="fw-semibold"> {{ $salary_deduct->deduct->name }} </div>
                <div class="d-flex fw-bold">
                    <i class="ki-duotone ki-arrow-down-left fs-3 me-1 text-danger">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>{{ $salary_deduct->amount }}
                </div>
            </div>
            <div class="separator separator-dashed"></div>
        @endforeach


    </div>
</div>










<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0">
        <!--begin::Card title-->
        <div class="card-title">
            <h2> @lang('hr::models/hr_contracts.plural') </h2>
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->

        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0 pb-5">
        <!--begin::Table wrapper-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                    <tr class="text-start text-muted text-uppercase gs-0">
                        <th class="min-w-100px">@lang('hr::models/hr_contracts.fields.type_id') </th>
                        <th>@lang('hr::models/hr_contracts.fields.qiwa_no')</th>
                        <th>@lang('hr::models/hr_contracts.fields.start_at')</th>
                        <th class="min-w-125px">@lang('hr::models/hr_contracts.fields.end_at')</th>

                    </tr>
                </thead>
                <tbody class="fs-6 fw-semibold text-gray-600">
                    @foreach ($Contract as $contract)
                        <tr>
                            <td>{{ $contract->type->name }}</td>
                            <td>{{ $contract->qiwa_no }}</td>
                            <td>{{ $contract->start_at }}</td>
                            <td>{{ $contract->end_at }}</td>

                        </tr>
                    @endforeach


                </tbody>
            </table>
            <!--end::Table-->
        </div>
        <!--end::Table wrapper-->
    </div>
    <!--end::Card body-->
</div>



{{-- <div class="card  mb-6 mb-xl-9">
    <div class="card-header">
        <h3 class="card-title  fw-bold"> @lang('hr::models/hr_contracts.plural') </h3>

    </div>
    <div class="card-body">
        <div class="row mb-6 mb-xl-9">

            @if (count($Contract) > 0)
                @foreach ($Contract as $contract)


                <div class="col-md-6 col-xxl-6">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card body-->
                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                            <!--begin::Avatar-->
                            <a href="{{ $contract->file_original_path }}"
                                class="text-gray-800 text-hover-primary d-flex flex-column">
                                <!--begin::Image-->

                                <div class="symbol symbol-60px mb-5">
                                    <img src="{{ asset('admin_assets') }}/media/svg/files/pdf.svg" class="theme-light-show"
                                        alt="">
                                    <img src="{{ asset('admin_assets') }}/media/svg/files/pdf-dark.svg"class="theme-dark-show" alt="">
                                </div>
                                <!--end::Image-->
                                <!--begin::Title-->
                                <div class="fs-5 fw-bold mb-2">@lang('hr::models/hr_contracts.fields.qiwa_no') : {{ $contract->qiwa_no }}</div>
                                <div class="fs-5 fw-bold mb-2">@lang('hr::models/hr_contracts.fields.type_id') : {{  $contract->type->name }}</div>
                                <!--end::Title-->
                            </a>
                            <!--end::Avatar-->
                            <!--begin::Name-->



                            <!--end::Position-->
                            <!--begin::Info-->
                            <div class="d-flex flex-center flex-wrap">
                                <!--begin::Stats-->
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                    <div class="fs-6 fw-bold text-gray-700"> {{ $contract->start_at }}</div>
                                    <div class="fw-semibold text-gray-500">@lang('hr::models/hr_contracts.fields.start_at') </div>
                                </div>
                                <!--end::Stats-->

                                <!--begin::Stats-->
                                <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                    <div class="fs-6 fw-bold text-gray-700">{{ $contract->end_at }} </div>
                                    <div class="fw-semibold text-gray-500">@lang('hr::models/hr_contracts.fields.end_at') </div>
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>



                @endforeach

            @endif

        </div>
    </div>

</div> --}}



<div class="card  mb-6 mb-xl-9">
    <div class="card-header">
        <h3 class="card-title  fw-bold"> @lang('hr::models/hr_documents.plural') </h3>

    </div>
    <div class="card-body">
        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">

            @if (count($Document) > 0)
                @foreach ($Document as $document)
                    <div class="col-md-6 col-lg-4 col-xl-4">
                        <!--begin::Card-->
                        <div class="card h-100">
                            <!--begin::Card body-->
                            <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                                <!--begin::Name-->
                                <a href="{{ $document->file_original_path }}"
                                    class="text-gray-800 text-hover-primary d-flex flex-column">
                                    <!--begin::Image-->

                                    <div class="symbol symbol-60px mb-5">
                                        <img src="{{ asset('admin_assets') }}/media/svg/files/pdf.svg" class="theme-light-show"
                                            alt="">
                                        <img src="{{ asset('admin_assets') }}/media/svg/files/pdf-dark.svg"
                                            class="theme-dark-show" alt="">
                                    </div>
                                    <!--end::Image-->
                                    <!--begin::Title-->
                                    <div class="fs-5 fw-bold mb-2">{{ $document->type->name }}</div>
                                    <!--end::Title-->
                                </a>
                                <!--end::Name-->
                                <!--begin::Description-->

                                <!--end::Description-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                @endforeach

            @endif

        </div>
    </div>

</div>


