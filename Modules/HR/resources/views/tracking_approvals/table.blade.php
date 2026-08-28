<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-tracking_approvals-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/modelVariable.fields.Trackable')</th>

                    <th>@lang('hr::models/modelVariable.fields.User Id')</th>

                    <th>@lang('hr::models/modelVariable.fields.Sort')</th>

                    <th>@lang('hr::models/modelVariable.fields.Is Current')</th>

                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tracking_approvals as $tracking_approval)
                <tr>
                    <td>{{ $tracking_approval->trackable }}</td>
                    <td>{{ $tracking_approval->user_id }}</td>
                    <td>{{ $tracking_approval->sort }}</td>
                    <td>{{ $tracking_approval->is_current }}</td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.tracking_approvals.destroy', $tracking_approval->id], 'method'
                        => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.tracking_approvals.show', [$tracking_approval->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.tracking_approvals.edit', [$tracking_approval->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $tracking_approvals])
        </div>
    </div>
</div>