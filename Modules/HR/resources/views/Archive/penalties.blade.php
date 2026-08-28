<div class="card pt-4 mb-6 mb-xl-9">
    <!--begin::Card header-->
    <div class="card-header border-0">
        <!--begin::Card title-->
        <div class="card-title">
            <h2> @lang('hr::models/hr_penalties.plural') </h2>
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
                        <th class="min-w-100px">@lang('hr::models/hr_penalties.fields.description') </th>
                        <th>@lang('hr::models/hr_penalties.fields.amount')</th>
                        <th>@lang('hr::models/hr_penalties.fields.due_date')</th>
                        <th>@lang('hr::models/hr_penalties.fields.status')</th>
                        <th>@lang('hr::models/hr_penalties.fields.created_at')</th>


                    </tr>
                </thead>
                <tbody class="fs-6 fw-semibold text-gray-600">
                    @foreach ($penalties as $penalty)
                        <tr>
                            <td>{{ $penalty->description }}</td>
                            <td>{{ $penalty->amount }}</td>
                            <td>{{ $penalty->due_date_text }}</td>
                            <td>
                                @livewire('hr::trackers.get-status', ['model' => $penalty], key('trackers_get_status'))
                            </td>
                            <td>{{ $penalty->created_at }}</td>
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



