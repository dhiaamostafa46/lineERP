<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0">
        <!--begin::Card title-->
        <div class="card-title">
            <h2> @lang('hr::models/hr_rewards.plural') </h2>
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
                        <th>@lang('hr::models/hr_rewards.fields.type')</th>
                        <th>@lang('hr::models/hr_rewards.fields.value')</th>
                        <th>@lang('hr::models/hr_rewards.fields.created_at')</th>
                    </tr>
                </thead>
                <tbody class="fs-6 fw-semibold text-gray-600">
                    @foreach ($rewards as $reward)
                        <tr>
                            <td>{{ $reward->type_text }}</td>
                            <td>{{ $reward->value_text }}</td>
                            <td>{{ $reward->created_at }}</td>
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




