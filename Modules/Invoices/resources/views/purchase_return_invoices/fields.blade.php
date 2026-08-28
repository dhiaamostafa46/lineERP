<div class="container-fluid py-4" id="purchase-invoice-app">
    {{-- Card 1: معلومات المرتجع الأساسية --}}
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-danger"></i>
                @lang('invoices::models/purchase_return_invoices.ui.basic_info')
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                {{-- المستودع --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        @lang('invoices::models/purchase_return_invoices.fields.branch_id')
                        <span class="text-danger">*</span>
                    </label>
                    <x-select2-input name="store_id"
                        :placeholder="__('invoices::models/purchase_return_invoices.fields.branch_id_placeholder')"
                        :list="$stores"
                        :selected_id="old('store_id', $purchaseReturn?->store_id ?? '')">
                    </x-select2-input>
                </div>

                {{-- المورد --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        @lang('invoices::models/purchase_return_invoices.fields.supplier_id')
                        <span class="text-danger">*</span>
                    </label>
                    {!! Form::select('supplier_id', [], old('supplier_id', $purchaseReturn?->supplier_id ?? $parentInvoice?->supplier_id ?? ''), ['class' => 'form-select select2-ajax-suppliers', 'data-selected' => old('supplier_id', $purchaseReturn?->supplier_id ?? $parentInvoice?->supplier_id ?? ''), 'placeholder' => __('invoices::models/purchase_return_invoices.fields.supplier_id_placeholder')]) !!}
                </div>


                
                @if(isset($selectedParentId) || isset($purchaseReturn->parent_id))
                {{-- حقل الربط المخفي في حال كان المرتجع من فاتورة محددة --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">@lang('invoices::models/purchase_return_invoices.fields.original_invoice_number')</label>
                    <input type="text" class="form-control form-control-solid bg-light" readonly 
                           value="{{ $parentInvoice?->invoice_number ?? $purchaseReturn?->parent?->invoice_number ?? old('parent_id', $selectedParentId ?? $purchaseReturn->parent_id) }}">
                </div>
                <input type="hidden" name="parent_id" value="{{ old('parent_id', $selectedParentId ?? $purchaseReturn->parent_id) }}">
                @endif

                {{-- رقم فاتورة المورد --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        @lang('invoices::models/purchase_return_invoices.fields.supplier_invoice_number')
                    </label>
                    {!! Form::text('supplier_invoice_number',
                        old('supplier_invoice_number', $purchaseReturn?->supplier_invoice_number ?? null),
                        ['class' => 'form-control', 'placeholder' => 'مثلاً: INV-2024']
                    ) !!}
                </div>

                {{-- تاريخ المرتجع --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        @lang('invoices::models/purchase_return_invoices.fields.issue_date')
                        <span class="text-danger">*</span>
                    </label>
                    {!! Form::datetimeLocal('issue_date', old('issue_date', $purchaseReturn?->issue_date ?? now()), [
                        'class' => 'form-control',
                        'required',
                    ]) !!}
                </div>

                {{-- الحالة --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        @lang('invoices::models/purchase_return_invoices.fields.status')
                        <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-control" required>
                        @php
                            $canDraft = auth()->user()->can('invoices.purchase_return.draft');
                            $canApprove = auth()->user()->can('invoices.purchase_return.approve');
                            $hasNeither = !$canDraft && !$canApprove;
                        @endphp
                        @if($canDraft || $hasNeither)
                            <option value="1" {{ old('status', $purchaseReturn?->status ?? ($canDraft ? 1 : 6)) == 1 ? 'selected' : '' }}>مسودة</option>
                        @endif
                        @if($canApprove || $hasNeither)
                            <option value="6" {{ old('status', $purchaseReturn?->status ?? ($canDraft ? 1 : 6)) == 6 ? 'selected' : '' }}>معتمدة</option>
                        @endif
                    </select>
                </div>

                {{-- سبب الإرجاع --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">@lang('invoices::models/purchase_return_invoices.fields.return_reason')</label>
                    {!! Form::textarea('return_reason',
                        old('return_reason', $purchaseReturn?->return_reason ?? ''),
                        ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('invoices::models/purchase_return_invoices.ui.return_reason_hint')]
                    ) !!}
                </div>
            </div>

            {{-- إخفاء type_inv = 2 --}}
            <input type="hidden" name="type_inv" value="2">
        </div>
    </div>

    {{-- Card 2: جدول الأصناف المرتجعة --}}
    @include('invoices::components.items_details', [
        'langPrefix' => 'invoices::models/purchase_return_invoices',
        'invoice'    => $purchaseReturn ?? null,
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
        {{-- Card 3: التبويبات --}}
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white h-100">
                <div class="card-header p-0 bg-transparent border-bottom-0" style="min-height: 0">
                    <ul class="nav nav-pills nav-pills-custom gap-3 p-4 pb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_payments">
                                <i class="ki-duotone ki-wallet fs-4 me-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                @lang('invoices::models/purchase_return_invoices.ui.payment_details')
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_discount">
                                <i class="ki-duotone ki-percentage fs-4 me-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                @lang('invoices::models/purchase_return_invoices.ui.invoice_discount')
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_attachments">
                                <i class="ki-duotone ki-paper-clip fs-4 me-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                @lang('invoices::models/purchase_return_invoices.ui.attachments')
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_shipping">
                                <i class="ki-duotone ki-delivery fs-4 me-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                {{ __('invoices::models/purchase_invoices.fields.shipping_cost') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        {{-- طرق الدفع / الاسترداد --}}
                        <div class="tab-pane fade show active" id="tab_payments" role="tabpanel">
                            @include('invoices::components.payment_methods', [
                                'langPrefix' => 'invoices::models/purchase_return_invoices',
                            ])
                        </div>
                        {{-- الخصم --}}
                        <div class="tab-pane fade" id="tab_discount" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">
                                        @lang('invoices::models/purchase_return_invoices.fields.additional_discount_value')
                                    </label>
                                    <div class="input-group">
                                        {!! Form::number('number_discount',
                                            old('number_discount', $purchaseReturn?->number_discount ?? 0),
                                            ['class' => 'form-control', 'id' => 'total_invoice_discount_input', 'step' => '0.01', 'oninput' => 'calcTotals()']
                                        ) !!}
                                        <select name="type_discount" id="total_invoice_discount_type"
                                            class="form-select" style="max-width: 100px" onchange="calcTotals()">
                                            <option value="1">%</option>
                                            <option value="2">@lang('invoices::models/purchase_return_invoices.fields.discount_type_fixed')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- المرفقات --}}
                        <div class="tab-pane fade" id="tab_attachments" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">@lang('invoices::models/purchase_return_invoices.ui.attachments')</label>
                                    <div class="upload-area p-4 border border-2 border-dashed rounded text-center bg-light position-relative">
                                        <label for="invoice_file" class="cursor-pointer d-block" style="cursor: pointer;">
                                            <i class="bi bi-cloud-arrow-up text-danger" style="font-size: 3rem;"></i>
                                            <p class="mb-1 fw-bold text-muted">@lang('invoices::models/purchase_return_invoices.ui.upload_hint')</p>
                                        </label>
                                        <input type="file" name="file" id="invoice_file" class="d-none"
                                            onchange="document.getElementById('file_name').innerText = this.files[0].name">
                                        <small id="file_name" class="text-success fw-bold d-block mt-2"></small>
                                        @if(isset($purchaseReturn) && $purchaseReturn->file_url)
                                            <div class="mt-3">
                                                <a href="{{ $purchaseReturn->file_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye me-1"></i> @lang('invoices::models/purchase_return_invoices.ui.view_attachment')
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Shipping Tab -->
                        <div class="tab-pane fade" id="tab_shipping" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_invoices.fields.shipping_cost') }}</label>
                                    <div class="input-group">
                                        {!! Form::number('shipping_cost', old('shipping_cost', $purchaseReturn?->shipping_cost ?? 0), [
                                            'class' => 'form-control',
                                            'id' => 'shipping_cost',
                                            'step' => '0.01',
                                            'oninput' => 'calcTotals()',
                                        ]) !!}
                                        <span class="input-group-text bg-light">{{ __('invoices::models/purchase_invoices.fields.currency_symbol') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/purchase_invoices.fields.shipping_vat_rate') }}</label>
                                    <select name="shipping_tax_id" id="shipping_tax_id" class="form-select" onchange="calcTotals()">
                                       
                                        @foreach($taxes ?? [] as $tax_id => $name)
                                            @php $taxRate = isset($taxes_data[$tax_id]) ? $taxes_data[$tax_id]['rate'] : 0; @endphp
                                            <option value="{{ $tax_id }}" data-rate="{{ $taxRate }}" {{ old('shipping_tax_id', $purchaseReturn?->shipping_tax_id ?? null) == $tax_id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="shipping_vat_rate" id="shipping_vat_rate_hidden" value="{{ old('shipping_vat_rate', $purchaseReturn?->shipping_vat_rate ?? 0) }}">
                                </div>
                                <input type="hidden" name="shipping_vat_amount" id="shipping_vat_amount" value="{{ old('shipping_vat_amount', $purchaseReturn?->shipping_vat_amount ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hidden Totals --}}
            <input type="hidden" name="total_exclusive_vat" id="total_exclusive_vat" value="0">
            <input type="hidden" name="total_discount" id="total_discount" value="0">
            <input type="hidden" name="total_vat" id="total_vat" value="0">
            <input type="hidden" name="total_inclusive_vat" id="total_inclusive_vat" value="0">
        </div>

        @push('scripts')
            @include('invoices::components.invoice_scripts', [
                'langPrefix'    => 'invoices::models/purchase_return_invoices',
                'getProductUrl' => route('Lookup.getproducts'),
                'isSale' => false,
            ])
        @endpush
