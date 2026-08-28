<div class="card-body p-0">
    <div class="table-responsive">

        <table class="table table-striped text-center gy-7 gs-7" id="db-products-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                    {{-- <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#db-products-table .form-check-input" value="1" />
                        </div>
                    </th> --}}
                    <th>@lang('basicdata::models/db_products.fields.name')</th>
                    <th>@lang('basicdata::models/db_products.fields.category_id')</th>
                    <th>@lang('basicdata::models/db_products.fields.type')</th>
                    <th>@lang('basicdata::models/db_products.fields.cost_price')</th>
                    <th>@lang('basicdata::models/db_products.fields.prod_price')</th>
                    <th>@lang('basicdata::models/db_products.fields.status')</th>
                    <th class="text-center table-action" >@lang('crud.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <img src="{{ $product->imgThumbPath }}" class="" alt="{{ $product->name }}">
                                </div>
                                <div class="d-flex justify-content-start flex-column">
                                    <a href="{{ route('basicdata.products.show', [$product->id]) }}" class="text-dark fw-bold text-hover-primary fs-6">{{ $product->name }}</a>
                                    <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $product->barcode }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name ?? '' }}</td>
                        <td>{{ $product->type_text }}</td>
                        <td>{{ $product->cost_price }}</td>
                        <td>{{ $product->prod_price }}</td>
                        <td>
                            <span class="badge {{ $product->status_badge }}">{{ $product->status_text }}</span>
                        </td>

                        <td style="width: 130px" class="table-action">
                            {!! Form::open(['route' => ['basicdata.products.destroy', $product->id], 'method' => 'delete']) !!}
                            <div class='d-inline-flex align-items-center gap-1'>
                                <a href="{{ route('basicdata.products.show', [$product->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-primary w-30px h-30px rounded-2'
                                    title="@lang('crud.view')">
                                    <i class="fa-solid fa-eye fs-7"></i>
                                </a>
                                <a href="{{ route('basicdata.products.edit', [$product->id]) }}"
                                    class='btn btn-icon btn-sm btn-light-success w-30px h-30px rounded-2'
                                    title="@lang('crud.edit')">
                                    <i class="fa-solid fa-pen-to-square fs-7"></i>
                                </a>
                                {!! Form::button('<i class="fa-solid fa-trash-can fs-7 text-danger"></i>', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-icon btn-sm btn-light-danger w-30px h-30px rounded-2',
                                    'title' => __('crud.delete'),
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
            @include('adminlte-templates::common.paginate', ['records' => $products])
        </div>
    </div>
</div>



