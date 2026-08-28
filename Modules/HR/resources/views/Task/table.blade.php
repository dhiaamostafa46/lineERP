<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-Task-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('hr::models/hr_tasks.fields.id')</th>
                    <th>@lang('hr::models/hr_tasks.fields.employee_id')/@lang('hr::models/hr_tasks.fields.department')/@lang('hr::models/hr_tasks.fields.Group')</th>
                    <th>@lang('hr::models/hr_tasks.fields.title')</th>
                    <th>@lang('hr::models/hr_tasks.fields.done')</th>
                    <th>@lang('hr::models/hr_tasks.fields.status')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Tasks as $Task)
                <tr>
                    <td>{{ $Task->id }}</td>
                    <td>{{ $Task->employee->username ?? $Task->Department->name ?? $Task->Group->name  ?? 'null'}}</td>
                    <td>{{ $Task->title }}</td>
                    <td>{{ $Task->done }}</td>
                    <td><span class="{{ $Task->status_badge }}">{{ $Task->status_text }}</span></td>

                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.Task.destroy', $Task->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('hr.Task.show')
                            <a href="{{ route('hr.Task.show', [$Task->id]) }}" class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('hr.Task.edit')
                            <a href="{{ route('hr.Task.edit', [$Task->id]) }}" class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.Task.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                            'type' => 'submit',
                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                            'onclick' => "return confirm('هل أنت متأكد؟')",
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

    <div class="card-footer clearfix py-4 {{ $Tasks->total() < 10 ? 'd-none' : '' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $Tasks])
        </div>
    </div>
</div>
