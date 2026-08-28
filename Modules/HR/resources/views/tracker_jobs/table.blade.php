<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-tracker-jobs-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/modelVariable.fields.Tracker Id')</th>

                    <th>@lang('hr::models/modelVariable.fields.Job Id')</th>

                    <th>@lang('hr::models/modelVariable.fields.Status')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tracker_jobs as $tracker_job)
                <tr>
                    <td>{{ $tracker_job->tracker_id }}</td>
                    <td>{{ $tracker_job->job_id }}</td>
                    <td>{{ $tracker_job->status }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.tracker-jobs.destroy', $tracker_job->id], 'method' =>
                        'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.tracker-jobs.show', [$tracker_job->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.tracker-jobs.edit', [$tracker_job->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $tracker_jobs])
        </div>
    </div>
</div>