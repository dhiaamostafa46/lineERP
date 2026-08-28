<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-Branches-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/Branches.fields.id')</th>
                    <th>@lang('models/Branches.fields.name')</th>
                    <th>@lang('models/Branches.fields.phone')</th>
                    <th>@lang('models/Branches.fields.area')</th>
                    <th>@lang('models/Branches.fields.city')</th>
                    <th>@lang('models/Branches.fields.district')</th>
                    <th>@lang('models/Branches.fields.address')</th>
                    <!--<th>@lang('models/Branches.fields.status')</th>-->
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Branches as $Branch)
                <tr>
                    <td>{{ $Branch->id }}</td>
                    <td>{{ $Branch->name }}</td>
                    <td>{{ $Branch->phone }}</td>
                    <td>{{ $Branch->area }}</td>
                    <td>{{ $Branch->city }}</td>
                    <td>{{ $Branch->district }}</td>
                    <td>{{ $Branch->address }}</td>
                    <!--<td><span class="{{ $Branch->status_badge }}">{{ $Branch->status_text }}</span></td>-->


                    <td style="width: 120px">
                        {!! Form::open(['route' => ['Branches.destroy', $Branch->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('Branches.show')
                            <a href="{{ route('Branches.show', [$Branch->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('Branches.edit')
                            <a href="{{ route('Branches.edit', [$Branch->id]) }}"
                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('Branches.destroy')
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

    <div class="card-footer clearfix py-4 {{ $Branches->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $Branches])
        </div>
    </div>
</div>

