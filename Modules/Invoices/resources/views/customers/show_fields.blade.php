
{{-- حقول الاسم بكل اللغات --}}
@foreach (config('langs') as $locale => $language)
    <div class="col-sm-12 row mb-4">
        <div class="col-4 my-auto">
            <label class="fs-6 fw-bold text-gray-700 mb-0">
                <i class="bi bi-translate me-2"></i> {{ $language }} @lang('invoices::models/inv_customers.fields.name')
            </label>
        </div>
        <div class="col-8">
            <div class="form-control bg-light border-0">{{ $customer->translate($locale)->name ?? '---' }}</div>
        </div>
    </div>
@endforeach

<div class="separator separator-dashed my-5"></div>

{{-- الهاتف --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-telephone me-2"></i> @lang('invoices::models/inv_customers.fields.phone')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $customer->phone ?? '---' }}</div>
    </div>
</div>

{{-- البريد الإلكتروني --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-envelope me-2"></i> @lang('invoices::models/inv_customers.fields.email')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $customer->email ?? '---' }}</div>
    </div>
</div>

{{-- الرقم الضريبي --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-receipt me-2"></i> @lang('invoices::models/inv_customers.fields.vat_number')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $customer->vat_number ?? '---' }}</div>
    </div>
</div>

{{-- الرقم التجاري --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-building me-2"></i> @lang('invoices::models/inv_customers.fields.cr_number')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $customer->cr_number ?? '---' }}</div>
    </div>
</div>

{{-- الدولة والمدينة --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-globe me-2"></i> @lang('invoices::models/inv_customers.fields.country')
        </label>
    </div>
    <div class="col-8">
        @php
            $countryName = __('countries')[$customer->country] ?? $customer->country;
        @endphp
        <div class="form-control bg-light border-0">{{ $countryName ?? '---' }}</div>
    </div>
</div>

<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-geo me-2"></i> @lang('invoices::models/inv_customers.fields.city') / @lang('invoices::models/inv_customers.fields.district')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">
            {{ $customer->city ?? '---' }} {{ $customer->district ? ' - ' . $customer->district : '' }}
        </div>
    </div>
</div>

{{-- الشارع ورقم المبنى --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-signpost-split me-2"></i> @lang('invoices::models/inv_customers.fields.street')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">
            {{ $customer->street ?? '---' }}
            @if($customer->building_number)
                ( @lang('invoices::models/inv_customers.fields.building_number'): {{ $customer->building_number }} )
            @endif
        </div>
    </div>
</div>

{{-- الرمز البريدي والرقم الإضافي --}}
@if($customer->postal_code || $customer->additional_number)
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-mailbox me-2"></i> @lang('invoices::models/inv_customers.fields.postal_code')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">
            {{ $customer->postal_code ?? '---' }}
            @if($customer->additional_number)
                | @lang('invoices::models/inv_customers.fields.additional_number'): {{ $customer->additional_number }}
            @endif
        </div>
    </div>
</div>
@endif

{{-- العنوان --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-geo-alt me-2"></i> @lang('invoices::models/inv_customers.fields.address')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0 h-auto">{{ $customer->address ?? '---' }}</div>
    </div>
</div>

{{-- الحالة --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-check-circle me-2"></i> @lang('invoices::models/inv_customers.fields.status')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">
            <span class="badge {{ $customer->status == 1 ? 'badge-light-success' : 'badge-light-danger' }}">
                {{ $customer->status == 1 ? __('lang.active') : __('lang.inactive') }}
            </span>
        </div>
    </div>
</div>

{{-- المرفق --}}
@if($customer->file)
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-paperclip me-2"></i> @lang('invoices::models/inv_customers.fields.file')
        </label>
    </div>
    <div class="col-8">
        <div class="d-flex align-items-center">
            <a href="{{ $customer->file_url }}" target="_blank" class="btn btn-sm btn-light-primary me-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> @lang('lang.show_file')
            </a>
            @php $ext = pathinfo($customer->file, PATHINFO_EXTENSION); @endphp
            @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                <div class="symbol symbol-50px border border-secondary p-1">
                    <img src="{{ $customer->file_url }}" alt="Attachment" class="cursor-pointer" onclick="window.open(this.src)">
                </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- تاريخ الإنشاء --}}
<div class="col-sm-12 row mb-4">
    <div class="col-4 my-auto">
        <label class="fs-6 fw-bold text-gray-700 mb-0">
            <i class="bi bi-calendar-event me-2"></i> @lang('crud.created_at')
        </label>
    </div>
    <div class="col-8">
        <div class="form-control bg-light border-0">{{ $customer->created_at ? $customer->created_at->format('Y-m-d H:i') : '---' }}</div>
    </div>
</div>
