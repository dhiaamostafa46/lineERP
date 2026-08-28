<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-holiday-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_holiday_types.fields.name')</th>
                    <th>@lang('hr::models/hr_holiday_types.fields.off_days')</th>
                    <th>@lang('hr::models/hr_holiday_types.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holiday_types as $holiday_type)
                <tr>
                    <td>{{ $holiday_type->name }}</td>
                    <td>{{ $holiday_type->off_days }}</td>
                    <td><span class="{{ $holiday_type->status_badge }}">{{ $holiday_type->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.holiday_types.destroy', $holiday_type->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            {{-- @can('hr.holiday_types.show')
                            <a href="{{ route('hr.holiday_types.show', [$holiday_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            --}}
                            @can('hr.holiday_types.edit')
                            <a href="{{ route('hr.holiday_types.edit', [$holiday_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.holiday_types.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('Are you sure?')",
                            ]) !!}
                            @endcan
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $holiday_types])
        </div>
    </div>
</div>
