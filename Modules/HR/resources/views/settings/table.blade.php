<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-settings-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_settings.fields.preparing_payroll_at')</th>
                    <th>@lang('hr::models/hr_settings.fields.delivery_payroll_at')</th>
                    <th>@lang('hr::models/hr_settings.fields.min_salary')</th>
                    <th>@lang('hr::models/hr_settings.fields.max_off_days')</th>
                    <th>@lang('hr::models/hr_settings.fields.currency')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settings as $setting)
                <tr>
                    <td>{{ $setting->preparing_at }}</td>
                    <td>{{ $setting->delivery_at }}</td>
                    <td>{{ $setting->min_salary }}</td>
                    <td>{{ $setting->max_off_days }}</td>
                    <td>{{ $setting->currency }}</td>
                    <td style="width: 120px">
                        {{-- {!! Form::open(['route' => ['hr.settings.destroy', $setting->id], 'method' => 'delete']) !!} --}}
                        <div class='btn-group'>
                            {{-- <a href="{{ route('hr.settings.show', [$setting->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                            @can('hr.settings.edit')
                            <a href="{{ route('hr.settings.edit', [$setting->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            {{-- {!! Form::button('<i class="fa-solid fa-trash"></i>', ['type' => 'submit', 'class' => 'btn
                            btn-icon btn-sm btn-light-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"])
                            !!} --}}
                        </div>
                        {{-- {!! Form::close() !!} --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $settings])
        </div>
    </div>
</div>
