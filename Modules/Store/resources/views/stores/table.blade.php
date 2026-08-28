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
                    <th>@lang('store::models/st_stores.fields.name')</th>
                    <th>@lang('store::models/st_stores.fields.branch_id')</th>
                    <th>@lang('store::models/st_stores.fields.manager_user_id')</th>
                    <th>@lang('store::models/st_stores.fields.address')</th>
                       <th>@lang('store::models/st_stores.fields.type')</th>
                    <th>@lang('store::models/st_stores.fields.status')</th>
                       <th>@lang('store::models/st_stores.fields.created_at')</th>
                    <th class="text-center table-action" >@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stores as $store)
                    <tr>
                        {{-- <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="{{ $category->id }}" />
                            </div>
                        </td> --}}
                        <td>{{ $store->name }}</td>
                        <td>{{ $store->branch?->name }}</td>
                        <td>{{ optional($store->managerUser)->name ?? '-' }}</td>
                        <td>{{ $store->address }}</td>
                        <td>{{ $store->type_text }}</td>
                        <td>
                            <span class="badge {{ $store->status_badge }}">{{ $store->status_text }}</span>
                        </td>
                         <td>{{ $store->created_at }}</td>
                        <td style="width: 150px" class="table-action">
                            {!! Form::open(['route' => ['store.stores.destroy', $store->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{{ route('store.stores.show', [$store->id]) }}"
                                    class='btn btn-sm btn-primary float-right' title="@lang('crud.show')">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @can('store.stores.print')
                                <a href="{{ route('store.stores.show', [$store->id]) }}?print=1" target="_blank"
                                    class='btn btn-sm btn-info float-right mx-1' title="@lang('lang.print')">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @endcan
                                <a href="{{ route('store.stores.edit', [$store->id]) }}"
                                    class='btn btn-sm btn-primary float-right mx-1'>
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-sm btn-primary float-right',
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
            @include('adminlte-templates::common.paginate', ['records' => $stores])
        </div>
    </div>
</div>
