<div class="card-body p-0">
    <div class="table-responsive">
        @php
            use Modules\HR\App\Models\HrAdvance;
        @endphp
        <table class="table table-striped gy-7 gs-7" id="hr-advances-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_advances.fields.employee_id')</th>
                    <th>@lang('hr::models/hr_advances.fields.amount')</th>
                    <th>@lang('hr::models/hr_advances.fields.from_date')</th>
                    <th>@lang('hr::models/hr_advances.fields.to_date')</th>
                    <th>@lang('hr::models/hr_advances.fields.reason')</th>
                    <th>@lang('hr::models/hr_advances.fields.status')</th>
                    <th colspan="2" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>


            <tbody>
                @foreach ($advances as $advance)
                    <tr>
                        <td>{{ $advance->employee->username ?? '' }}</td>
                        <td>{{ $advance->amount }}</td>
                        <td>{{ $advance->from_date->format('Y-m-d') }}</td>
                        <td>{{ $advance->to_date->format('Y-m-d')}}</td>
                        <td>{{ $advance->reason }}</td>
                        <td>
                            <span class="badge {{ $advance->status_badge }}">{{ $advance->status_text }}</span>
                        </td>
                        <td style="width: 120px">
                            {!! Form::open(['route' => ['hr.advances.destroy', $advance->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('hr.advances.show', [$advance->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if ($advance->status == HrAdvance::STATUS_PENDING)
                                    <a href="{{ route('hr.advances.edit', [$advance->id]) }}"
                                        class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                        'onclick' => "return confirm('Are you sure?')",
                                    ]) !!}
                                @endif
                            </div>
                            {!! Form::close() !!}
                        </td>
                        <td>
                            @livewire('hr::payrolls.add-transactions', ['hr_setting' => $hr_setting, 'model' => $advance, 'showAddButton' => $advance->status == HrAdvance::STATUS_APPROVED], key('add-transactions-' . $advance->id))
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $advances])
        </div>
    </div>
</div>
