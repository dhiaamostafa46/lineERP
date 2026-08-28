<div class="card-body p-0">
    <div class="table-responsive">
        @php
            use Modules\HR\App\Models\HrAttendancePolicy;
        @endphp
        <table class="table table-striped gy-7 gs-7" id="hr-attendance-policies-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_attendance_policies.fields.name')</th>
                    <th>@lang('hr::models/hr_attendance_policies.fields.type')</th>
                    <th>@lang('hr::models/hr_attendance_policies.fields.scope')</th>
                    {{-- <th>@lang('hr::models/hr_attendance_policies.fields.calculation_type')</th> --}}
                    <th>@lang('hr::models/hr_attendance_policies.fields.is_automatic')</th>
                    <th>@lang('hr::models/hr_attendance_policies.fields.status')</th>
                    <th colspan="2" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>


            <tbody>
                @foreach ($policies as $policy)
                    <tr>
                        <td>{{ $policy->name }}</td>
                        <td>{{ $policy->type_text }}</td>
                        <td>{{ $policy->scope_text }}</td>
                            {{-- <td>{{ $policy->calculationType_text }}</td> --}}
                        {{-- <td>
                            {{ $policy->CalculationType_ == 'day' ? __('hr::lang.daily') : __('hr::lang.hourly') }}
                        </td> --}}
                        <td>
                            <span class="badge badge-light-{{ $policy->is_automatic ? 'success' : 'warning' }}">
                                {{ $policy->is_automatic_text }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $policy->status_badge }}">{{ $policy->status_text }}</span>
                        </td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['hr.attendance-policies.destroy', $policy->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('hr.attendance-policies.show', [$policy->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                    <a href="{{ route('hr.attendance-policies.edit', [$policy->id]) }}"
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
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $policies])
        </div>
    </div>
</div>
