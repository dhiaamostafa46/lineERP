<div class="border-0 rounded-4">
    <div class="card-body p-4">
        <div class="row g-4">
            {{-- 🧾 بيانات التواصل الأساسية --}}
            <div class="col-12">
                <h5 class="text-primary d-flex align-items-center mb-0">
                    <span class="bg-primary-subtle p-2 rounded-3 me-3">
                        <i class="bi bi-person-lines-fill fs-5"></i>
                    </span>
                    {{ __('invoices::models/inv_customers.sections.contact_info') }}
                </h5>
                <hr class="mt-3 mb-1 opacity-50">
            </div>

            {{-- حقول الترجمة (الاسم بعدة لغات) --}}
            @foreach (config('langs') as $locale => $language)
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        {!! Form::label($locale . '[name]', $language . ' ' . __('invoices::models/inv_customers.fields.name'), [
                            'class' => 'form-label fw-semibold mb-2',
                        ]) !!}
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-translate text-muted"></i>
                            </span>
                            {!! Form::text($locale . '[name]', old($locale . '.name', isset($customer) ? $customer->translate($locale)->name ?? null : null), [
                                'class' => 'form-control bg-light border-start-0 ps-0',
                                'id' => $locale . '_name',
                                'placeholder' => __('invoices::models/inv_customers.fields.name'),
                            ]) !!}
                        </div>
                        @error($locale . '.name')
                            <div class="invalid-feedback d-block mt-1 small text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            @endforeach

            {{-- الهاتف --}}
            <div class="col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('phone', __('invoices::models/inv_customers.fields.phone'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-telephone-fill text-muted"></i>
                        </span>
                        {!! Form::text('phone', old('phone', $customer?->phone ?? null), [
                            'class' => 'form-control bg-light',
                            'placeholder' => __('invoices::models/inv_customers.fields.phone'),
                        ]) !!}
                    </div>
                    @error('phone')
                        <div class="invalid-feedback d-block mt-1 small text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- البريد الإلكتروني --}}
            <div class="col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('email', __('invoices::models/inv_customers.fields.email'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-envelope-fill text-muted"></i>
                        </span>
                        {!! Form::email('email', old('email', $customer?->email ?? null), [
                            'class' => 'form-control bg-light',
                            'placeholder' => __('invoices::models/inv_customers.fields.email'),
                        ]) !!}
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block mt-1 small text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- الحالة --}}
            <div class="col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('status', __('invoices::models/inv_customers.fields.status'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <x-select2-input name="status" :placeholder="__('lang.select')" :list="[1 => __('lang.active'), 0 => __('lang.inactive')]" :selected_id="old('status', $customer?->status ?? 1)">
                    </x-select2-input>
                    @error('status')
                        <div class="invalid-feedback d-block mt-1 small text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- 🏢 بيانات ضريبية --}}
            <div class="col-12 mt-4">
                <h5 class="text-primary d-flex align-items-center mb-0">
                    <span class="bg-primary-subtle p-2 rounded-3 me-3">
                        <i class="bi bi-building-check fs-5"></i>
                    </span>
                    {{ __('invoices::models/inv_customers.sections.tax_info') }}
                </h5>
                <hr class="mt-3 mb-1 opacity-50">
            </div>

            {{-- الرقم الضريبي --}}
            <div class="col-md-6">
                <div class="form-group mb-0">
                    {!! Form::label('vat_number', __('invoices::models/inv_customers.fields.vat_number'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-receipt text-muted"></i>
                        </span>
                        {!! Form::text('vat_number', old('vat_number', $customer?->vat_number ?? null), [
                            'class' => 'form-control bg-light',
                            'placeholder' => __('invoices::models/inv_customers.fields.vat_number'),
                        ]) !!}
                    </div>
                    @error('vat_number')
                        <div class="invalid-feedback d-block mt-1 small text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- الرقم التجاري --}}
            <div class="col-md-6">
                <div class="form-group mb-0">
                    {!! Form::label('cr_number', __('invoices::models/inv_customers.fields.cr_number'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-card-checklist text-muted"></i>
                        </span>
                        {!! Form::text('cr_number', old('cr_number', $customer?->cr_number ?? null), [
                            'class' => 'form-control bg-light',
                            'placeholder' => __('invoices::models/inv_customers.fields.cr_number'),
                        ]) !!}
                    </div>
                    @error('cr_number')
                        <div class="invalid-feedback d-block mt-1 small text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- 📍 العنوان الوطني والتفصيلي --}}
            <div class="col-12 mt-4">
                <h5 class="text-primary d-flex align-items-center mb-0">
                    <span class="bg-primary-subtle p-2 rounded-3 me-3">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </span>
                    {{ __('invoices::models/inv_customers.sections.address_info') }}
                </h5>
                <hr class="mt-3 mb-1 opacity-50">
            </div>

            {{-- الدولة والمدينة --}}
            <div class="col-md-3">
                <div class="form-group mb-0">
                    {!! Form::label('country', __('invoices::models/inv_customers.fields.country'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <x-select2-input name="country" :placeholder="__('lang.select')" :list="__('countries')" :selected_id="old('country', $customer?->country ?? 'SA')">
                    </x-select2-input>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-0">
                    {!! Form::label('city', __('invoices::models/inv_customers.fields.city'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    {!! Form::text('city', old('city', $customer?->city ?? null), [
                        'class' => 'form-control bg-light',
                        'placeholder' => __('invoices::models/inv_customers.fields.city'),
                    ]) !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-0">
                    {!! Form::label('district', __('invoices::models/inv_customers.fields.district'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    {!! Form::text('district', old('district', $customer?->district ?? null), [
                        'class' => 'form-control bg-light',
                        'placeholder' => __('invoices::models/inv_customers.fields.district'),
                    ]) !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-0">
                    {!! Form::label('street', __('invoices::models/inv_customers.fields.street'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    {!! Form::text('street', old('street', $customer?->street ?? null), [
                        'class' => 'form-control bg-light',
                        'placeholder' => __('invoices::models/inv_customers.fields.street'),
                    ]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('building_number', __('invoices::models/inv_customers.fields.building_number'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    {!! Form::text('building_number', old('building_number', $customer?->building_number ?? null), [
                        'class' => 'form-control bg-light',
                        'placeholder' => __('invoices::models/inv_customers.fields.building_number'),
                    ]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('postal_code', __('invoices::models/inv_customers.fields.postal_code'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    {!! Form::text('postal_code', old('postal_code', $customer?->postal_code ?? null), [
                        'class' => 'form-control bg-light',
                        'placeholder' => __('invoices::models/inv_customers.fields.postal_code'),
                    ]) !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group mb-0">
                    {!! Form::label('additional_number', __('invoices::models/inv_customers.fields.additional_number'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    {!! Form::text('additional_number', old('additional_number', $customer?->additional_number ?? null), [
                        'class' => 'form-control bg-light',
                        'placeholder' => __('invoices::models/inv_customers.fields.additional_number'),
                    ]) !!}
                </div>
            </div>

            {{-- 📎 المرفقات --}}
            <div class="col-12 mt-4">
                <h5 class="text-primary d-flex align-items-center mb-0">
                    <span class="bg-primary-subtle p-2 rounded-3 me-3">
                        <i class="bi bi-paperclip fs-5"></i>
                    </span>
                    {{ __('invoices::models/inv_customers.sections.attachments') }}
                </h5>
                <hr class="mt-3 mb-1 opacity-50">
            </div>

            <div class="col-12">
                <div class="form-group mb-0">
                    {!! Form::label('file', __('invoices::models/inv_customers.fields.file'), [
                        'class' => 'form-label fw-semibold mb-2',
                    ]) !!}
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-cloud-arrow-up text-muted"></i>
                        </span>
                        {!! Form::file('file', ['class' => 'form-control bg-light']) !!}
                    </div>
                   
                    @if (isset($customer) && $customer->file)
                        <div class="mt-2 small">
                            <a href="{{$customer->file_url }}" target="_blank"
                                class="text-primary text-decoration-none">
                                <i class="bi bi-file-earmark-check me-1"></i> عرض الملف الحالي
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
