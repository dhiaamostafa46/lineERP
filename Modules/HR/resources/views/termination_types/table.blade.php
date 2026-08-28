<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-termination-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_termination_types.fields.name')</th>
                    <th>@lang('hr::models/hr_termination_types.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($termination_types as $termination_type)
                <tr>
                    <td>{{ $termination_type->name }}</td>
                    <td>
                        <span class="{{ $termination_type->status_badge }}">
                            {{ $termination_type->status_text }}
                        </span>
                    </td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.termination_types.destroy', $termination_type->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.termination_types.show', [$termination_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.termination_types.edit', [$termination_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', ['type' => 'submit', 'class' => 'btn
                            btn-icon btn-sm btn-light-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"])
                            !!}
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
            @include('adminlte-templates::common.paginate', ['records' => $termination_types])
        </div>
    </div>
</div>
