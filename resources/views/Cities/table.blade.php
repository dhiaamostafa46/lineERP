<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="cities-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('models/Cities.fields.id')</th>
                    <th>@lang('models/Cities.fields.code')</th>
                    <th>@lang('models/Cities.fields.name')</th>
                    <th>@lang('models/Cities.fields.area')</th>
                    <th>@lang('models/Cities.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($Cities as $City)
                <tr>
                    <td>{{ $City->id }}</td>
                    <td>{{ $City->code }}</td>
                    <td>{{ $City->name }}</td>
                    <td>{{ optional($City->area)->name ?? '—' }}</td>
                    <td><span class="{{ $City->status_badge }}">{{ $City->status_text }}</span></td>
                    <td style="width: 120px">
                        {!! Form::open(['route' => ['Cities.destroy', $City->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @can('Cities.show')
                            <a href="{{ route('Cities.show', [$City->id]) }}"
                                class='btn btn-icon btn-sm btn-primary btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @endcan
                            @can('Cities.edit')
                            <a href="{{ route('Cities.edit', [$City->id]) }}"
                                class='btn btn-icon btn-sm btn-primary mx-1  btn-xs'>
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            @endcan
                            @can('Cities.destroy')
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
    <div class="card-footer clearfix py-4 {{ $Cities->hasPages() ? '' : 'd-none' }}">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $Cities])
        </div>
    </div>
</div>
