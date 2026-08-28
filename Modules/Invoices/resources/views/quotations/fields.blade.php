<div class="container-fluid py-4" id="quotation-app">
    <!-- Card 1: Basic Information -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                {{ __('invoices::models/quotations.ui.basic_info') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('invoices::models/quotations.fields.store_id') }}
                        <span class="text-danger">*</span>
                    </label>
                    <x-select2-input name="store_id" 
                        :placeholder="__('invoices::models/quotations.fields.store_id')" 
                        :list="$stores" 
                        :selected_id="old('store_id', $quotation?->store_id ?? '')">
                    </x-select2-input>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('invoices::models/quotations.fields.customer_id') }}
                        <span class="text-danger">*</span>
                    </label>
                    {!! Form::select('customer_id', [], old('customer_id', $quotation?->customer_id ?? ''), ['class' => 'form-select select2-ajax-customers', 'data-selected' => old('customer_id', $quotation?->customer_id ?? ''), 'placeholder' => __('invoices::models/quotations.fields.customer_id')]) !!}
                </div>
                {{-- <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('invoices::models/quotations.fields.status') }}
                        <span class="text-danger">*</span>
                    </label>
                    <select name="status" class="form-control" required>
                        @foreach(\Modules\Invoices\App\Models\Quotation::statuses() as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $quotation->status ?? 'new') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('invoices::models/quotations.fields.issue_date') }}
                        <span class="text-danger">*</span>
                    </label>
                    {!! Form::datetimeLocal('issue_date', old('issue_date', isset($quotation->issue_date) ? $quotation->issue_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')), [
                        'class' => 'form-control',
                        'required',
                    ]) !!}
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('invoices::models/quotations.fields.expiry_date') }}
                    </label>
                    {!! Form::date('expiry_date', old('expiry_date', isset($quotation->expiry_date) ? $quotation->expiry_date->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')), [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Items Table -->
    @include('invoices::components.items_details', [
        'langPrefix' => 'invoices::models/quotations',
        'invoice' => $quotation ?? null,
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
        <!-- Card 3: Tabs (Discounts, Attachments, etc.) -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white h-100">
                <div class="card-header p-0 bg-transparent border-bottom-0" style="min-height: 0">
                    <ul class="nav nav-pills nav-pills-custom gap-3 p-4 pb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_discount">
                                <i class="ki-duotone ki-percentage fs-4 me-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('invoices::models/quotations.ui.invoice_discount') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_cost_center">
                                <i class="ki-duotone ki-category fs-4 me-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                {{ __('invoices::models/quotations.ui.cost_center') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_attachments">
                                <i class="ki-duotone ki-paper-clip fs-4 me-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('invoices::models/quotations.ui.attachments') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center py-3 px-4 fs-7 fw-bold" data-bs-toggle="tab" href="#tab_shipping">
                                <i class="ki-duotone ki-delivery fs-4 me-2 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                {{ __('invoices::models/quotations.fields.shipping_cost') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="myTabContent">
                        <!-- 1. Invoice Discount Tab -->
                        <div class="tab-pane fade show active" id="tab_discount" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">{{ __('invoices::models/quotations.fields.additional_discount_value') }}</label>
                                    <div class="input-group">
                                        {!! Form::number(
                                            'number_discount',
                                            old('number_discount', $quotation?->number_discount ?? 0),
                                            ['class' => 'form-control', 'id' => 'total_invoice_discount_input', 'step' => '0.01', 'oninput' => 'calcTotals()'],
                                        ) !!}
                                        <select name="type_discount" id="total_invoice_discount_type" class="form-select" style="max-width: 100px" onchange="calcTotals()">
                                            <option value="1" {{ old('type_discount', $quotation?->type_discount ?? 1) == 1 ? 'selected' : '' }}>%</option>
                                            <option value="2" {{ old('type_discount', $quotation?->type_discount ?? 1) == 2 ? 'selected' : '' }}>{{ __('invoices::models/quotations.fields.discount_type_fixed') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Cost Center Tab -->
                        <div class="tab-pane fade" id="tab_cost_center" role="tabpanel">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">{{ __('invoices::models/quotations.ui.cost_center') }}</label>
                                <x-select2-input name="cost_center_id" 
                                    :placeholder="__('invoices::models/quotations.ui.cost_center')" 
                                    :list="$cost_centers ?? []" 
                                    :selected_id="old('cost_center_id', $quotation?->cost_center_id ?? '')">
                                </x-select2-input> 
                            </div>
                        </div>

                        <!-- 3. Attachments & Notes Tab -->
                        <div class="tab-pane fade" id="tab_attachments" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/quotations.fields.notes') }}</label>
                                    {!! Form::textarea('notes', old('notes', $quotation?->notes ?? null), [
                                        'class' => 'form-control',
                                        'rows' => 2,
                                        'placeholder' => __('invoices::models/quotations.ui.notes_placeholder') ?? '...',
                                    ]) !!}
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/quotations.fields.payment_terms') }}</label>
                                    {!! Form::textarea('payment_terms', old('payment_terms', $quotation?->payment_terms ?? null), [
                                        'class' => 'form-control',
                                        'rows' => 2,
                                        'placeholder' => __('invoices::models/quotations.ui.payment_terms_placeholder'),
                                    ]) !!}
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/quotations.fields.validity_period') }}</label>
                                    {!! Form::text('validity_period', old('validity_period', $quotation?->validity_period ?? null), [
                                        'class' => 'form-control',
                                        'placeholder' => __('invoices::models/quotations.ui.validity_period_placeholder'),
                                    ]) !!}
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">{{ __('invoices::models/quotations.ui.attachments') }}</label>
                                    <div class="upload-area p-4 border border-2 border-dashed rounded text-center bg-light position-relative">
                                        <label for="quotation_file" class="cursor-pointer d-block" style="cursor: pointer;">
                                            <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 3rem;"></i>
                                            <p class="mb-1 fw-bold text-muted">اضغط هنا لرفع المرفق</p>
                                        </label>
                                        <input type="file" name="file" id="quotation_file" class="d-none" onchange="document.getElementById('file_name').innerText = this.files[0].name">
                                        <small id="file_name" class="text-success fw-bold d-block mt-2"></small>
                                        
                                        @if(isset($quotation) && $quotation->file_url)
                                            <div class="mt-3">
                                                <a href="{{ $quotation->file_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye me-1"></i> عرض الملف المرفق الحالي
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
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/quotations.fields.shipping_cost') }}</label>
                                    <div class="input-group">
                                        {!! Form::number('shipping_cost', old('shipping_cost', $quotation?->shipping_cost ?? 0), [
                                            'class' => 'form-control',
                                            'id' => 'shipping_cost',
                                            'step' => '0.01',
                                            'oninput' => 'calcTotals()',
                                        ]) !!}
                                        <span class="input-group-text bg-light">﷼</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">{{ __('invoices::models/quotations.fields.shipping_vat_rate') }}</label>
                                    <select name="shipping_tax_id" id="shipping_tax_id" class="form-select" onchange="calcTotals()">
                                        
                                        @foreach($taxes ?? [] as $tax_id => $name)
                                            @php $taxRate = isset($taxes_data[$tax_id]) ? $taxes_data[$tax_id]['rate'] : 0; @endphp
                                            <option value="{{ $tax_id }}" data-rate="{{ $taxRate }}" {{ old('shipping_tax_id', $quotation?->shipping_tax_id ?? null) == $tax_id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="shipping_vat_rate" id="shipping_vat_rate_hidden" value="{{ old('shipping_vat_rate', $quotation?->shipping_vat_rate ?? 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Total Inputs for Controller -->
            <input type="hidden" name="total_exclusive_vat" id="total_exclusive_vat" value="{{ old('total_exclusive_vat', $quotation?->total_exclusive_vat ?? 0) }}">
            <input type="hidden" name="total_discount" id="total_discount" value="{{ old('total_discount', $quotation?->total_discount ?? 0) }}">
            <input type="hidden" name="total_vat" id="total_vat" value="{{ old('total_vat', $quotation?->total_vat ?? 0) }}">
            <input type="hidden" name="total_inclusive_vat" id="total_inclusive_vat" value="{{ old('total_inclusive_vat', $quotation?->total_inclusive_vat ?? 0) }}">
            <input type="hidden" name="shipping_vat_amount" id="shipping_vat_amount" value="{{ old('shipping_vat_amount', $quotation?->shipping_vat_amount ?? 0) }}">
        </div>
    </div>
</div>

@push('scripts')
    @include('invoices::components.invoice_scripts', [
        'langPrefix' => 'invoices::models/quotations',
        'getProductUrl' => route('Lookup.getproducts'),
        'isSale' => true,
    ])
@endpush
