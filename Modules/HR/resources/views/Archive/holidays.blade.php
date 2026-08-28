<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0">
        <!--begin::Card title-->
        <div class="card-title">
            <h2> @lang('hr::models/hr_holidays.plural') </h2>
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

                        <th>@lang('hr::models/hr_holidays.fields.type_id')</th>
                        <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                        <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                        <th>@lang('hr::models/hr_holidays.fields.status')</th>



                    </tr>
                </thead>
                <tbody class="fs-6 fw-semibold text-gray-600">
                    @foreach ($holidays as $holiday)
                        <tr>
                            <td>{{ $holiday->type->name ?? '' }}</td>
                            <td>{{ $holiday->from_at }}</td>
                            <td>{{ $holiday->end_at }}</td>
                            <td>
                                <span class="{{  $holiday->status_badge }}"> {{  $holiday->status_text }} </span>

                            </td>
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



<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0">
        <!--begin::Card title-->
        <div class="card-title">
            <h2> @lang('hr::models/hr_absentrequest.plural') </h2>
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

                    <th>@lang('hr::models/hr_absentrequest.fields.requestdate')</th>
                    <th>@lang('hr::models/hr_holidays.fields.from_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.end_at')</th>
                    <th>@lang('hr::models/hr_holidays.fields.detials')</th>
                    <th>@lang('hr::models/hr_holidays.fields.status')</th>


                    </tr>
                </thead>
                <tbody class="fs-6 fw-semibold text-gray-600">
                    @foreach ($AbsentRequests as $absent)
                        <tr>
                            <td>{{ $absent->request_date->format('Y-m-d')}}</td>
                            <td>{{ $absent->from_at}}</td>
                            <td>{{ $absent->end_at}}</td>
                            <td>{{ $absent->details ?? '' }}</td>
                            <td>
                                <span class="{{  $absent->status_badge }}"> {{  $absent->status_text }} </span>
                              
                            </td>
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



