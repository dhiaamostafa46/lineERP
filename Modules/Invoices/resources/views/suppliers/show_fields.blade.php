{{-- حقول الاسم بكل اللغات --}}
@foreach (config('langs') as $locale => $language)
    <div class="col-sm-12 row mb-4">
        <div class="col-4 my-auto">
            <label class="fs-6 fw-bold text-gray-700 mb-0">
                <i class="bi bi-translate me-2"></i> {{ $language }} @lang('invoices::models/inv_suppliers.fields.name')
            </label>
        </div>
        <div class="col-8">
            <div class="form-control bg-light border-0">{{ $supplier->translate($locale)->name ?? '---' }}</div>
        </div>
    </div>
@endforeach

<div class="separator separator-dashed my-5"></div>

{{-- الهاتف والبريد --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-telephone me-2"></i> @lang('invoices::models/inv_suppliers.fields.phone')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $supplier->phone ?? '---' }}</div>
    </div>
</div>

<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-envelope me-2"></i> @lang('invoices::models/inv_suppliers.fields.email')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $supplier->email ?? '---' }}</div>
    </div>
</div>

<div class="separator separator-dashed my-5"></div>

{{-- البيانات الضريبية --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-receipt me-2"></i> @lang('invoices::models/inv_suppliers.fields.vat_number')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $supplier->vat_number ?? '---' }}</div>
    </div>
</div>

<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-building me-2"></i> @lang('invoices::models/inv_suppliers.fields.cr_number')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $supplier->cr_number ?? '---' }}</div>
    </div>
</div>

<div class="separator separator-dashed my-5"></div>

{{-- بيانات العنوان --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-geo-alt me-2"></i> @lang('invoices::models/inv_suppliers.sections.address_info')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0 h-auto">
            <strong>{{ __('countries')[$supplier->country] ?? $supplier->country }}</strong>, {{ $supplier->city }}<br>
            {{ $supplier->district }} - {{ $supplier->street }}<br>
            @if($supplier->building_number) @lang('invoices::models/inv_suppliers.fields.building_number'): {{ $supplier->building_number }} @endif
            @if($supplier->postal_code) | @lang('invoices::models/inv_suppliers.fields.postal_code'): {{ $supplier->postal_code }} @endif
        </div>
    </div>
</div>

<div class="separator separator-dashed my-5"></div>

{{-- البيانات المالية --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-diagram-3 me-2"></i> @lang('invoices::models/inv_suppliers.fields.tree_account_id')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">
            {{ $supplier->treeAccount->name ?? '---' }}
            @if($supplier->treeAccount) <span class="text-muted small">({{ $supplier->treeAccount->code }})</span> @endif
        </div>
    </div>
</div>

<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-cash-stack me-2"></i> @lang('invoices::models/inv_suppliers.fields.credit_limit')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ number_format($supplier->credit_limit, 2) }}</div>
    </div>
</div>

<div class="separator separator-dashed my-5"></div>

{{-- الحالة والمرفقات --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-info-circle me-2"></i> @lang('invoices::models/inv_suppliers.fields.status')
        </label>
    </div>
    <div class="col-8">
        <span class="{{ $supplier->status_badge }}">{{ $supplier->status_text }}</span>
    </div>
</div>

@if($supplier->file)
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-paperclip me-2"></i> @lang('invoices::models/inv_suppliers.fields.file')
        </label>
    </div>
    <div class="col-8">
        <div class="d-flex align-items-center">
            <a href="{{ $supplier->file_url }}" target="_blank" class="btn btn-sm btn-light-primary me-3">
                <i class="bi bi-eye me-1"></i> @lang('lang.read_more')
            </a>
        </div>
    </div>
</div>
@endif

{{-- تاريخ الإنشاء --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-calendar-check me-2"></i> @lang('invoices::models/inv_suppliers.fields.created_at')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">
            {{ $supplier->created_at ? $supplier->created_at->format('Y-m-d H:i') : '---' }}
        </div>
    </div>
</div>
