<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped gy-7 gs-7" id="hr-assets-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    <th>@lang('hr::models/hr_assets.fields.department_id')</th>
                    <th>@lang('hr::models/hr_assets.fields.type_id')</th>
                    <th>@lang('hr::models/hr_assets.fields.is_new')</th>
                    <th>@lang('hr::models/hr_assets.fields.name')</th>
                    <th>@lang('hr::models/hr_assets.fields.note')</th>
                    <th>@lang('hr::models/hr_assets.fields.status')</th>
                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $asset)
                <tr>
                    <td>{{ $asset->department->name ?? '' }}</td>
                    <td>{{ $asset->type->name ?? '' }}</td>
                    <td>{{ $asset->is_new ? __('lang.yes') : __('lang.no') }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->note }}</td>
                    <td>
                        <span class="{{ $asset->status_badge }}">
                            {{ $asset->status_text }}
                        </span>
                    </td>

                    <td style="width: 120px">
                        {!! Form::open(['route' => ['hr.assets.destroy', $asset->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('hr.assets.show', [$asset->id]) }}"
                                class='btn btn-icon btn-sm btn-light-success btn-xs'>
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.assets.edit', [$asset->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $assets])
        </div>
    </div>
</div>
