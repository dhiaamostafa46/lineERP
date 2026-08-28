<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="areas-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/Areas.fields.id')</th>
                    <th>@lang('models/Areas.fields.code')</th>
                    <th>@lang('models/Areas.fields.name')</th>
                    <th>@lang('models/Areas.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Areas as $Area)
                <tr>
                    <td>{{ $Area->id }}</td>
                    <td>{{ $Area->code }}</td>
                    <td>{{ $Area->name }}</td>
                    <td><span class="{{ $Area->status_badge }}">{{ $Area->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['Areas.destroy', $Area->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('Areas.show')
                            <a href="{{ route('Areas.show', [$Area->id]) }}"
                                class='btn btn-icon btn-sm btn-primary btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('Areas.edit')
                            <a href="{{ route('Areas.edit', [$Area->id]) }}"
                                class='btn btn-icon btn-sm btn-primary mx-1  btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('Areas.destroy')
                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                'type' => 'submit',
                                'class' => 'btn btn-icon btn-sm btn-primary   btn-xs',
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
    <div class="card-footer clearfix py-4 {{ $Areas->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $Areas])
        </div>
    </div>
</div>
