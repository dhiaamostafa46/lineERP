<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="db-categories-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    {{-- <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#db-categories-table .form-check-input" value="1" />
                        </div>
                    </th> --}}
                    <th>@lang('store::models/st_damageds.fields.document_number')</th>
                    <th>@lang('store::models/st_damageds.fields.document_date')</th>
                    <th>@lang('store::models/st_damageds.fields.store_id')</th>
                    <th>@lang('store::models/st_damageds.fields.total_value')</th>
                    <th>@lang('store::models/st_damageds.fields.status')</th>
                    <th class="text-center min-w-100px">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($damageds as $damaged)
                    <tr>
                        <td>{{ $damaged->document_number }}</td>
                        <td>{{ $damaged->document_date->format('Y-m-d') }}</td>
                        <td>{{ $damaged->store->name ?? '' }}</td>
                        <td>{{ number_format($damaged->total_value, 2) }}</td>
                        <td>{{ $damaged->status_text }}</td>

                         <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['store.damaged.destroy', $damaged->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('store.damaged.show', [$damaged->id]) }}"
                                    class='btn btn-sm btn-primary float-right' title="@lang('crud.show')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.damaged.print')
                                <a href="{{ route('store.damaged.show', [$damaged->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-primary float-right mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan
                                @if($damaged->is_editable)
                                <a href="{{ route('store.damaged.edit', [$damaged->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                @endif
                                @if($damaged->is_deletable)
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
                                    'onclick' => "return confirm('Are you sure?')",
                                ]) !!}
                                @endif
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
            @include('adminlte-templates::common.paginate', ['records' => $damageds])
        </div>
    </div>
</div>
