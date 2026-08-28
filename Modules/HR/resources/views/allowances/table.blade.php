<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-allowances-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_allowances.fields.name')</th>
                    <th>@lang('hr::models/hr_allowances.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allowances as $allowance)
                <tr>
                    <td>{{ $allowance->name }}</td>
                    <td><span class="{{ $allowance->status_badge }}">{{ $allowance->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.allowances.destroy', $allowance->id], 'method' => 'delete'])
                        !!}
                        <div class='btn-group'>
                            @can('hr.allowances.show')
                            {{-- <a href="{{ route('hr.allowances.show', [$allowance->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                            @endcan
                            @can('hr.allowances.edit')
                            <a href="{{ route('hr.allowances.edit', [$allowance->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.allowances.destroy')
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
            @include('adminlte-templates::common.paginate', ['records' => $allowances])
        </div>
    </div>
</div>
