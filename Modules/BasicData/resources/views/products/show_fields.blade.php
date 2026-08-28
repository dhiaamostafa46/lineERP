<!--begin::Card header-->
<div class="card-header cursor-pointer">
    <!--begin::Card title-->
    {{-- <div class="card-title m-0">
        <h3 class="fw-bold m-0">@lang('crud.detail')</h3>
    </div> --}}
    <!--end::Card title-->
    <div class="card-toolbar">
        <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#details_tab">@lang('crud.detail')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#units_tab">@lang('basicdata::models/db_units.plural')</a>
            </li>
            @if ($product->have_sizes)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sizes_tab">@lang('basicdata::models/db_products.sizes')</a>
            </li>
            @endif
            {{-- <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tracking_tab">التتبع</a>
            </li> --}}
        </ul>
    </div>
</div>
<!--end::Card header-->


<!--begin::Card body-->
<div class="card-body p-9">
    <div class="tab-content">
        <!-- Details Tab -->
        <div class="tab-pane fade show active" id="details_tab" role="tabpanel">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6 ">
                    <!-- Id Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.id')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->id }}</span>
                        </div>
                    </div>

                    <!-- Name Field -->
                    <div class="row mb-7 ">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.name')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->name }}</span>
                        </div>
                    </div>

                    <!-- Category Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.category_id')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->category->name ?? '' }}</span>
                        </div>
                    </div>

                    <!-- Type Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.type')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->type_text }}</span>
                        </div>
                    </div>

                    <!-- Min Quantity Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.min_quantity')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->min_quantity }}</span>
                        </div>
                    </div>

                    <!-- Details Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.details')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->details ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Cost Price Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.cost_price')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ number_format($product->cost_price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Sale Price Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.prod_price')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ number_format($product->prod_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- VAT Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.vat')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->vat }}%</span>
                        </div>
                    </div>

                    <!-- Work From Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.s_from')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->s_from ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Work To Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.s_to')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->s_to ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Calories Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.calories')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->calories ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.status')</label>
                        <div class="col-lg-8 ">
                              <span class="fw-bold fs-6 form-control text-gray-800">
                                 <span class="badge {{ $product->status_badge }}">{{ $product->status_text }}</span>
                              </span>

                        </div>
                    </div>

                    <!-- Created At Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.created_at')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->created_at->format('Y-m-d H:i A') }}</span>
                        </div>
                    </div>

                    <!-- Updated At Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.updated_at')</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 form-control text-gray-800">{{ $product->updated_at->format('Y-m-d H:i A') }}</span>
                        </div>
                    </div>


                </div>
                <div class="col-12">

                       <!-- Image Field -->
                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.img')</label>
                        <div class="col-lg-8">
                            @if ($product->img)
                                <div class="symbol symbol-100px symbol-lg-160px">
                                    <img src="{{ $product->imgThumbPath }}" alt="Product Image">
                                </div>
                            @else
                                <div class="symbol symbol-100px symbol-2by3">
                                    <div class="symbol-label fs-2 fw-semibold bg-light-danger text-danger">No Image</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-7">
                        <label class="col-lg-4 ">@lang('basicdata::models/db_products.fields.barcode')</label>
                        <div class="col-lg-8">
                            @if ($product->barcode)
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        <svg id="barcode_{{ $product->id }}" class="mb-2"></svg>
                                        <span class="fw-bold fs-6 text-gray-800 d-block mt-1">{{ $product->barcode }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary" title="طباعة الباركود" onclick="printBarcode('{{ $product->barcode }}', '{{ $product->name }}', '{{ number_format($product->prod_price, 2) }}')">
                                        <i class="bi bi-printer fs-3"></i>
                                    </button>
                                </div>
                            @else
                                <span class="fw-bold fs-6 form-control text-gray-800">N/A</span>
                            @endif
                        </div>
                    </div>


                </div>


            </div>
        </div>

        <!-- Units Tab -->
        <div class="tab-pane fade" id="units_tab" role="tabpanel">
            <h3 class="mb-5">@lang('basicdata::models/db_products.sections.units')</h3>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 gy-4">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th>@lang('basicdata::models/db_products.unit.unit_id')</th>
                            <th>@lang('basicdata::models/db_products.unit.conversion_factor')</th>
                            <th>@lang('basicdata::models/db_products.unit.is_base')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->units as $unit)
                            <tr>
                                <td>{{ $unit->unit->name ?? 'N/A' }}</td>
                                <td>{{ $unit->conversion_factor }}</td>
                                <td>{{ $unit->is_base_text }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    @lang('messages.no_data_found', ['model' => __('basicdata::models/db_products.sections.units')])
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sizes Tab -->
        @if ($product->have_sizes)
        <div class="tab-pane fade" id="sizes_tab" role="tabpanel">
            <h3 class="mb-5">@lang('basicdata::models/db_products.sections.sizes')</h3>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 gy-4">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800">
                            <th>@lang('basicdata::models/db_products.size.name')</th>
                            <th>@lang('basicdata::models/db_products.size.barcode')</th>
                            <th>@lang('basicdata::models/db_products.size.cost_price')</th>
                            <th>@lang('basicdata::models/db_products.size.sale_price')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->sizes as $size)
                            <tr>
                                <td>{{ $size->name }}</td>
                                <td>
                                    @if($size->barcode)
                                        <div class="d-flex align-items-center gap-2">
                                            <span>{{ $size->barcode }}</span>
                                            <button type="button" class="btn btn-sm btn-icon btn-light-primary" title="طباعة الباركود" onclick="printBarcode('{{ $size->barcode }}', '{{ $product->name }} ({{ $size->name }})', '{{ number_format($size->sale_price, 2) }}')">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ number_format($size->cost_price, 2) }}</td>
                                <td>{{ number_format($size->sale_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    @lang('messages.no_data_found', ['model' => __('basicdata::models/db_products.sections.sizes')])
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Tracking Tab -->
        <div class="tab-pane fade" id="tracking_tab" role="tabpanel">
            {{-- Tracking content goes here --}}
            <div class="text-center p-10">
                <h4 class="text-muted">@lang('messages.no_data_found', ['model' => 'التتبع'])</h4>
            </div>
        </div>
    </div>
</div>
<!--end::Card body-->

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if ($product->barcode)
            JsBarcode("#barcode_{{ $product->id }}", "{{ $product->barcode }}", {
                format: "CODE128",
                displayValue: false,
                width: 2,
                height: 50,
                margin: 10
            });
        @endif
    });

    function printBarcode(barcode, name, price) {
        const printWindow = window.open('', '_blank', 'width=600,height=400');
        const currencySymbol = "{{ __('invoices::models/purchase_invoices.fields.currency_symbol') }}";
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Barcode - ${barcode}</title>
                    <style>
                        @page { size: auto; margin: 0; }
                        body { 
                            font-family: 'Arial', sans-serif; 
                            text-align: center; 
                            padding: 10px;
                            margin: 0;
                            direction: rtl;
                        }
                        .label {
                            border: 1px dashed #ccc;
                            padding: 10px;
                            display: inline-block;
                            width: 60mm;
                            height: 40mm;
                            box-sizing: border-box;
                        }
                        .name { font-size: 14px; font-weight: bold; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                        .price { font-size: 16px; font-weight: bold; margin-top: 5px; }
                        svg { width: 100%; height: auto; max-height: 20mm; }
                    </style>
                    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                </head>
                <body>
                    <div class="label">
                        <div class="name">${name}</div>
                        <svg id="barcode_print"></svg>
                        <div class="price">${price} ${currencySymbol}</div>
                    </div>
                    <script>
                        window.onload = function() {
                            JsBarcode("#barcode_print", "${barcode}", {
                                format: "CODE128",
                                displayValue: true,
                                fontSize: 12,
                                width: 2,
                                height: 50,
                                margin: 0
                            });
                            setTimeout(() => {
                                window.print();
                                window.close();
                            }, 500);
                        };
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
