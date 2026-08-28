<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-custodies-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_custodies.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_custodies.fields.asset_id')</th>
                    <th>@lang('hr::models/hr_custodies.fields.details')</th>
                    <th>@lang('hr::models/hr_custodies.fields.received_id')</th>
                    <th>@lang('hr::models/hr_custodies.fields.received_at')</th>
                    <th>@lang('hr::models/hr_custodies.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($custodies as $custody)
                <tr>
                    <td>{{ $custody->employee->username ?? '' }}</td>
                    <td>{{ $custody->asset->name ?? '' }}</td>
                    <td>{{ $custody->details }}</td>
                    <td>{{ $custody->receiver->username ?? '' }}</td>
                    <td>{{ $custody->received_at }}</td>
                    <td>
                        <span class="{{ $custody->status_badge }}">
                            {{ $custody->status_text }}
                        </span>
                    </td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.custodies.destroy', $custody->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.custodies.show', [$custody->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.custodies.edit', [$custody->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @if($custody->status ==3)
                                <a href="{{ route('hr.custodies.AcceptReturn', [$custody->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-warning btn-xs'>
                                    <i class="fa-regular fa-square-check"></i>
                                </a>
                                <a href="{{ route('hr.custodies.nonAccept', [$custody->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-info btn-xs'>
                                    <i class="fa-sharp fa-solid fa-rotate-right"></i>
                                </a>
                            @endif
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
            @include('adminlte-templates::common.paginate', ['records' => $custodies])
        </div>
    </div>
</div>
