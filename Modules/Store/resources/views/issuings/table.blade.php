<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-striped text-center gy-7 gs-7" id="issuing-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                    <th>@lang('store::models/st_issuings.fields.document_number')</th>
                    <th>@lang('store::models/st_issuings.fields.document_date')</th>
                    <th>@lang('store::models/st_issuings.fields.store_id')</th>
                    <th>@lang('store::models/st_issuings.fields.total_items')</th>
                    <th>@lang('store::models/st_issuings.fields.total_quantity')</th>
                    <th>@lang('store::models/st_issuings.fields.total_value')</th>
                    <th>@lang('store::models/st_issuings.fields.status')</th>
                    <th class="text-center min-w-100px table-action">@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody class="fs-6 fw-semibold text-gray-600">
                @foreach ($issuings as $issuing)
                    <tr>
                        <td>{{ $issuing->document_number }}</td>
                        <td>{{ $issuing->document_date->format('Y-m-d') }}</td>
                        <td>{{ $issuing->store->name ?? '' }}</td>
                        <td>{{ $issuing->total_items }}</td>
                        <td>{{ number_format($issuing->total_quantity, 2) }}</td>
                        <td>{{ number_format($issuing->total_value, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $issuing->status == 2 ? 'success' : 'warning' }}">
                                {{ $issuing->status_text }}
                            </span>
                        </td>
                        <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['store.issuing.destroy', $issuing->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('store.issuing.show', [$issuing->id]) }}"
                                    class='btn btn-sm btn-primary float-right' title="@lang('crud.show')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.issuing.print')
                                <a href="{{ route('store.issuing.show', [$issuing->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-primary float-right mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan
                                @if ($issuing->is_editable)
                                    <a href="{{ route('store.issuing.edit', [$issuing->id]) }}"
                                        class='btn btn-sm btn-primary float-right mx-1'>
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                @endif
                                @if ($issuing->is_deletable)
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
            @include('adminlte-templates::common.paginate', ['records' => $issuings])
        </div>
    </div>
</div>
