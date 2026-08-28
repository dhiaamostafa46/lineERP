<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-jobs-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_jobs.fields.id')</th>
                    <th>@lang('hr::models/hr_jobs.fields.name')</th>
                    <th>@lang('hr::models/hr_jobs.fields.license_required')</th>
                    <th>@lang('hr::models/hr_jobs.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->name }}</td>
                    <td><span class="{{ $job->license_badge }}">{{ $job->license_text }}</span></td>
                    <td><span class="{{ $job->status_badge }}">{{ $job->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.jobs.destroy', $job->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('hr.jobs.show')
                            {{-- <a href="{{ route('hr.jobs.show', [$job->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a> --}}
                            @endcan

                            @can('hr.jobs.edit')
                            <a href="{{ route('hr.jobs.edit', [$job->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('hr.jobs.destroy')
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

    <div class="card-footer clearfix py-4 {{ $jobs->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $jobs])
        </div>
    </div>
</div>
