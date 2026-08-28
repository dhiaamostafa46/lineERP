<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-report-types-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_report_types.fields.name')</th>
                    <th>@lang('hr::models/hr_report_types.fields.description')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report_types as $report_type)
                <tr>
                    <td>{{ $report_type->name }}</td>
                    <td>{{ $report_type->description }}</td>
                    <td style="width: 120px">
                        <div class='btn-group'>
                            @can('hr.report_types.show')
                            {{-- <a href="{{ route('hr.report_types.show', [$report_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                            @endcan
                            @can('hr.report_types.export')
                            <a href="{{ route('hr.report_types.export', [$report_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-file-export"></i>
                            </a>
                            @endcan
                            @can('hr.report_types.edit')
                            <a href="{{ route('hr.report_types.edit', [$report_type->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix py-4">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $report_types])
        </div>
    </div>
</div>