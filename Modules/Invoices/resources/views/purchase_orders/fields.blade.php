<div class="container-fluid py-4" id="purchase-invoice-app">
    <!-- Card 1: Basic Information -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                {{ __('invoices::models/purchase_orders.ui.basic_info') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <label
                        class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_orders.fields.branch_id') }}
                        <span class="text-danger">*</span></label>
                    {{-- <select name="branch_id" class=" form-control  select2-general" required>
                        <option value="">
                            {{ __('invoices::models/purchase_orders.fields.branch_id_placeholder') }}</option>
                        @foreach ($branches ?? [] as $id => $name)
                            <option value="{{ $id }}"
                                {{ isset($purchaseOrder) && $purchaseOrder->branch_id == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select> --}}


                    <x-select2-input name="store_id" :placeholder=" __('invoices::models/purchase_orders.fields.branch_id_placeholder')" :list="$stores" :selected_id="old('store_id', $purchaseOrder?->store_id ?? '')">
                    </x-select2-input>

                </div>
                <div class="col-md-3">
                    <label
                        class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_orders.fields.supplier_id') }}
                        <span class="text-danger">*</span></label>
                      {!! Form::select('supplier_id', [], old('supplier_id', $purchaseOrder?->supplier_id ?? ''), ['class' => 'form-select select2-ajax-suppliers', 'data-selected' => old('supplier_id', $purchaseOrder?->supplier_id ?? ''), 'placeholder' => __('invoices::models/purchase_orders.fields.supplier_id_placeholder')]) !!}
                </div>
                {{-- <div class="col-md-3">
                    <label
                        class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_orders.fields.supplier_invoice_number') }}</label>
                    {!! Form::text(
                        'supplier_invoice_number',
                        old('supplier_invoice_number', $purchaseOrder?->supplier_invoice_number ?? null),
                        ['class' => 'form-control', 'placeholder' => 'Ù…Ø«Ù„Ø§Ù‹: INV-2024'],
                    ) !!}
                </div> --}}
                <div class="col-md-3">
                    <label
                        class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_orders.fields.issue_date') }}
                        <span class="text-danger">*</span></label>
                    {!! Form::datetimeLocal('issue_date', old('issue_date', $purchaseOrder?->issue_date ?? now()), [
                        'class' => 'form-control',
                        'required',
                    ]) !!}
                </div>
            </div>
            <input type="hidden" name="type_inv" value="1">
            {{-- <input type="hidden" name="invoice_number" value="{{ old('invoice_number', $purchaseOrder?->invoice_number ?? mt_rand(100000, 999999)) }}"> --}}
        </div>
    </div>

    <!-- Card 2: Items Table -->
    @include('invoices::components.items_details', [
        'langPrefix' => 'invoices::models/purchase_orders',
        'invoice' => $purchaseOrder ?? null,
    ])
    <style>
        .nav-pills-custom .nav-link { border: 1px solid transparent; transition: all 0.3s ease; color: #7E8299; }
        .nav-pills-custom .nav-link:hover { background-color: rgba(0, 158, 247, 0.05); }
        .nav-pills-custom .nav-link.active { 
            background-color: var(--bs-primary) !important; 
            border: 1px solid var(--bs-primary) !important; 
            color: #ffffff !important; 
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
        }
        .nav-pills-custom .nav-link.active i { color: #ffffff !important; }
    </style>
    <div class="row g-5">
        <!-- Card 3: Tabs (Payments, Discounts, etc.) -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white h-100">
                <div class="card-header p-0 bg-transparent border-bottom-0"  style="min-height: 0">
                    <ul class="nav nav-pills nav-pills-custom gap-3 p-4 pb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_discount">
                                <i class="ki-duotone ki-percentage fs-4 me-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('invoices::models/purchase_orders.ui.invoice_discount') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_attachments">
                                <i class="ki-duotone ki-paper-clip fs-4 me-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('invoices::models/purchase_orders.ui.attachments') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="myTabContent">
                        <!-- 1. Payments Tab -->
                        {{-- <div class="tab-pane fade show active" id="tab_payments" role="tabpanel">
                            @include('invoices::components.payment_methods', [
                                'langPrefix' => 'invoices::models/purchase_orders',
                            ])
                        </div> --}}
                        <!-- 2. Invoice Discount Tab -->
                        <div class="tab-pane fade show active" id="tab_discount" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label
                                        class="form-label fw-bold small">{{ __('invoices::models/purchase_orders.fields.additional_discount_value') }}</label>
                                    <div class="input-group">
                                        {!! Form::number(
                                            'number_discount',
                                            old('number_discount', $purchaseOrder?->number_discount ?? 0),
                                            ['class' => 'form-control', 'id' => 'total_invoice_discount_input', 'step' => '0.01', 'oninput' => 'calcTotals()'],
                                        ) !!}
                                        <select name="type_discount" id="total_invoice_discount_type"
                                            class="form-select" style="max-width: 100px" onchange="calcTotals()">
                                            <option value="1">%</option>
                                            <option value="2">
                                                {{ __('invoices::models/purchase_orders.fields.discount_type_fixed') }}
                                            </option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Cost Center Tab -->
                        {{-- <div class="tab-pane fade" id="tab_cost_center" role="tabpanel">
                            <div class="col-md-4">
                                <label
                                    class="form-label fw-bold small">{{ __('invoices::models/purchase_orders.fields.cost_center_id') ?? '' }}</label>
                                 <x-select2-input name="cost_center_id" :placeholder="__('invoices::models/purchase_orders.fields.cost_center_id') ?? '---'" :list="$cost_centers ?? []" :selected_id="old('cost_center_id', $purchaseOrder?->cost_center_id ?? '')">
                                </x-select2-input> 
                            </div>
                        </div> --}}

                        <!-- 4. Attachments Tab -->
                        <div class="tab-pane fade" id="tab_attachments" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label
                                        class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_orders.fields.notes') ?? 'الملاحظات' }}</label>
                                    {!! Form::textarea('notes', old('notes', $purchaseOrder?->notes ?? null), [
                                        'class' => 'form-control',
                                        'rows' => 2,
                                        'placeholder' => __('invoices::models/purchase_orders.fields.notes_placeholder') ?? 'إضافة ملاحظات إضافية...',
                                    ]) !!}
                                </div>

                                <div class="col-md-12">
                                    <label
                                        class="form-label fw-bold small">{{ __('invoices::models/purchase_orders.ui.attachments') }}</label>
                                    <div class="upload-area p-4 border border-2 border-dashed rounded text-center bg-light position-relative">
                                        <label for="invoice_file" class="cursor-pointer d-block" style="cursor: pointer;">
                                            <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 3rem;"></i>
                                            <p class="mb-1 fw-bold text-muted">اضغط هنا لرفع المرفق</p>
                                        </label>
                                        <input type="file" name="file" id="invoice_file" class="d-none" onchange="document.getElementById('file_name').innerText = this.files[0].name">
                                        <small id="file_name" class="text-success fw-bold d-block mt-2"></small>
                                        
                                        @if(isset($purchaseOrder) && $purchaseOrder->file_url)
                                            <div class="mt-3">
                                                <a href="{{ $purchaseOrder->file_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye me-1"></i> عرض الملف المرفق الحالي
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 4: Totals Summary -->

            </div>

            <!-- Hidden Total Inputs for Controller -->
            <input type="hidden" name="total_exclusive_vat" id="total_exclusive_vat" value="0">
            <input type="hidden" name="total_discount" id="total_discount" value="0">
            <input type="hidden" name="total_vat" id="total_vat" value="0">
            <input type="hidden" name="total_inclusive_vat" id="total_inclusive_vat" value="0">
        </div>
        @push('scripts')
            @include('invoices::components.invoice_scripts', [
                'langPrefix' => 'invoices::models/purchase_orders',
                'getProductUrl' => route('Lookup.getproducts'),
                'isSale' => false,
            ])
        @endpush
