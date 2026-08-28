<div class="container-fluid">

    <input type="hidden" name="type" id="type_product" value="{{ isset($product) ? $product->type : $type }}">
    <div class="accordion" id="productAccordion">
        <!-- Accordion Item 1: Basic Info -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingBasicInfo">
                <button class="accordion-button" type="button">
                    {{ __('basicdata::models/db_products.sections.basic_info') }}
                </button>
            </h2>
            <div id="collapseBasicInfo" class="accordion-collapse collapse show" aria-labelledby="headingBasicInfo">
                <div class="accordion-body">
                    <div class="row">
                        @foreach (config('langs') as $locale => $language)
                            <!-- Name Field -->
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label($locale . '[name]', $language . ' ' . __('basicdata::models/db_products.fields.name') . ':', [
                                    'class' => 'form-label fw-bold',
                                ]) !!}
                                {!! Form::text($locale . '[name]', isset($product) ? $product->translate($locale)->name : null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('basicdata::models/db_products.placeholders.name'),
                                ]) !!}
                            </div>
                        @endforeach



                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('barcode', __('basicdata::models/db_products.fields.barcode') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            {!! Form::text('barcode', $product->barcode ?? null, [
                                'class' => 'form-control',
                                'placeholder' => __('basicdata::models/db_products.placeholders.barcode'),
                            ]) !!}
                        </div>

                        <!-- Category -->
                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('category_id', __('basicdata::models/db_products.fields.category_id') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            <x-select2-input name="category_id" :placeholder="__('hr::lang.select_category')" :list="$categories" :selected_id="old('category_id', $product->category_id ?? (array_key_first($categories) ?? ''))" />
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('prod_price', __('basicdata::models/db_products.fields.prod_price') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            <div class="input-group">
                                <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                {!! Form::number('prod_price', $product->prod_price ?? null, [
                                    'class' => 'form-control',
                                    'step' => '0.01',
                                    'min' => 0,
                                    'placeholder' => '0.00',
                                ]) !!}
                            </div>
                        </div>
                        <!-- Cost Price -->
                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('cost_price', __('basicdata::models/db_products.fields.cost_price') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            <div class="input-group">
                                <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                {!! Form::number('cost_price', $product->cost_price ?? null, [
                                    'class' => 'form-control',
                                    'step' => '0.01',
                                    'min' => 0,
                                    'placeholder' => '0.00',
                                ]) !!}
                            </div>
                        </div>

                        <!-- VAT -->


                        <!-- Status -->
                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('status', __('basicdata::models/db_products.fields.status') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            <div class="d-flex">
                                @foreach ($statuses as $value => $label)
                                    <div class="form-check form-check-inline">
                                        {!! Form::radio('status', $value, old('status', $product->status ?? 1) == $value, [
                                            'class' => 'form-check-input',
                                            'id' => 'status_' . $value,
                                        ]) !!}
                                        {!! Form::label('status_' . $value, $label, ['class' => 'form-check-label']) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Have Sizes Switch -->



                        <!-- Image -->
                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('img', __('basicdata::models/db_products.fields.img') . ':', ['class' => 'form-label fw-bold']) !!}
                            {!! Form::file('img', ['class' => 'form-control', 'accept' => 'image/*']) !!}
                            @if (isset($product) && $product->img)
                                <div class="mt-3">
                                    <img src="{{ $product->imgThumbPath }}" alt="Product Image" class="img-thumbnail"
                                        style="max-height:150px;">
                                </div>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <!-- Accordion Item: Units -->


            <div class="accordion-item" id="units-accordion-item">
                <h2 class="accordion-header" id="headingUnits">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnits" aria-expanded="true" aria-controls="collapseUnits">
                        {{ __('basicdata::models/db_products.sections.units') }}
                    </button>
                </h2>
                <div id="collapseUnits" class="accordion-collapse collapse show" aria-labelledby="headingUnits">
                    <div class="accordion-body">
                        @error('units')
                            <div class="alert alert-danger p-2 mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i> {{ $message }}
                            </div>
                        @enderror
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addUnitRow()">
                                <i class="fas fa-plus"></i> {{ __('basicdata::models/db_products.sections.add_unit') }}
                            </button>
                        </div>
                        <div id="units-container" class="mt-4">
                            @if (isset($product) && $product->units && count($product->units) > 0)
                                @foreach ($product->units as $index => $unit)
                                    <div class="unit-row mb-3 p-3 border rounded" data-index="{{ $index }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                {!! Form::label('units[' . $index . '][unit_id]', __('basicdata::models/db_products.unit.unit_id') . ':', [
                                                    'class' => 'form-label fw-bold',
                                                ]) !!}
                                                <x-select2-input name="units[{{ $index }}][unit_id]"
                                                    :placeholder="__('basicdata::models/db_products.unit.unit_id')" :list="$units" :selected_id="old(
                                                        'units.' . $index . '.unit_id',
                                                        $unit->unit_id ?? '',
                                                    )" />
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                {!! Form::label(
                                                    'units[' . $index . '][conversion_factor]',
                                                    __('basicdata::models/db_products.unit.conversion_factor') . ':',
                                                    ['class' => 'form-label fw-bold'],
                                                ) !!}
                                                {!! Form::number('units[' . $index . '][conversion_factor]', old('units.' . $index . '.conversion_factor', $unit->conversion_factor ?? 1), [
                                                    'class' => 'form-control',
                                                    'step' => '0.01',
                                                    'min' => 0,
                                                    'placeholder' => '1.00',
                                                ]) !!}
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label
                                                    class="form-label fw-bold">{{ __('basicdata::models/db_products.unit.is_base') }}</label>
                                                <div class="form-check form-switch mt-2">
                                                    {!! Form::checkbox(
                                                        'units[' . $index . '][is_base]',
                                                        1,
                                                        old('units.' . $index . '.is_base', $unit->is_base ?? 0),
                                                        [
                                                            'id' => 'is_base_' . $index,
                                                            'class' => 'form-check-input is-base-checkbox',
                                                        ],
                                                    ) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-primary btn-sm w-100"
                                                    onclick="removeUnitRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="unit-row mb-3 p-3 border rounded" data-index="0">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            {!! Form::label('units[0][unit_id]', __('basicdata::models/db_products.unit.unit_id') . ':', [
                                                'class' => 'form-label fw-bold',
                                            ]) !!}
                                            <x-select2-input name="units[0][unit_id]" :placeholder="__('basicdata::models/db_products.unit.unit_id')"
                                                :list="$units" :selected_id="old('units.0.unit_id', '')" />
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            {!! Form::label(
                                                'units[0][conversion_factor]',
                                                __('basicdata::models/db_products.unit.conversion_factor') . ':',
                                                ['class' => 'form-label fw-bold'],
                                            ) !!}
                                            {!! Form::number('units[0][conversion_factor]', old('units.0.conversion_factor', 1), [
                                                'class' => 'form-control',
                                                'step' => '0.01',
                                                'min' => 0,
                                                'placeholder' => '1.00',
                                            ]) !!}
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label
                                                class="form-label fw-bold">{{ __('basicdata::models/db_products.unit.is_base') }}</label>
                                            <div class="form-check form-switch mt-2">
                                                {!! Form::checkbox('units[0][is_base]', 1, true, [
                                                    'class' => 'form-check-input is-base-checkbox',
                                                    'id' => 'is_base_0',
                                                    'class' => 'form-check-input is-base-checkbox',
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary btn-sm w-100"
                                                onclick="removeUnitRow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        <!-- Accordion Item: VAT Info -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingvatInfo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapsevatInfo" aria-expanded="false" aria-controls="collapsevatInfo">
                    {{ __('basicdata::models/db_products.sections.vat_info') }}
                </button>
            </h2>
            <div id="collapsevatInfo" class="accordion-collapse collapse" aria-labelledby="headingvatInfo">
                <div class="accordion-body">
                    <div class="row">
                        <!-- Kitchen -->


                        {{-- <div class="form-group col-md-6 mb-3">
                            {!! Form::label('vat', __('basicdata::models/db_products.fields.vat') . ':', ['class' => 'form-label fw-bold']) !!}
                            <div class="input-group">
                                {!! Form::number('vat', $product->vat ?? null, [
                                    'class' => 'form-control',
                                    'step' => '0.01',
                                    'min' => 0,
                                    'max' => 100,
                                    'placeholder' => '0.00',
                                ]) !!}
                            </div>
                        </div> --}}

                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('vat', __('basicdata::models/db_products.fields.vat') . ':', ['class' => 'form-label fw-bold']) !!}
                            <div class="input-group">
                                <x-select2-input name="tax_id" :placeholder="__('accusoft::models/tax_accounts.singular')" :list="$vats" :selected_id="old('tax_id', $product->tax_id ?? (array_key_first($vats) ?? null))" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- Accordion Item 3: Date Info -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingDateInfo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseDateInfo" aria-expanded="false" aria-controls="collapseDateInfo">
                    {{ __('basicdata::models/db_products.sections.other_info') }}
                </button>
            </h2>
            <div id="collapseDateInfo" class="accordion-collapse collapse" aria-labelledby="headingDateInfo">
                <div class="accordion-body">
                    <div class="row">

                        @foreach (config('langs') as $locale => $language)
                            <!-- Details Field -->
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label(
                                    $locale . '[details]',
                                    $language . ' ' . __('basicdata::models/db_products.fields.details') . ':',
                                    ['class' => 'form-label fw-bold'],
                                ) !!}
                                {!! Form::textarea($locale . '[details]', isset($product) ? $product->translate($locale)->details : null, [
                                    'class' => 'form-control',
                                    'rows' => 3,
                                    'placeholder' => __('basicdata::models/db_products.placeholders.details'),
                                ]) !!}
                            </div>
                        @endforeach
                        @if ($type != 2)
                            <div class="form-group col-md-6 mb-3">
                                {!! Form::label('min_quantity', __('basicdata::models/db_products.fields.min_quantity') . ':', [
                                    'class' => 'form-label fw-bold',
                                ]) !!}
                                <div class="input-group">
                                    {!! Form::number('min_quantity', $product->min_quantity ?? 0, [
                                        'class' => 'form-control',
                                        'step' => '0.01',
                                        'min' => 0,
                                        'placeholder' => '0.00',
                                    ]) !!}
                                </div>
                            </div>
                        @endif
                        <!-- Service Hours -->
                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('s_from', __('basicdata::models/db_products.fields.s_from') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            {!! Form::time('s_from', $product->s_from ?? null, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {!! Form::label('s_to', __('basicdata::models/db_products.fields.s_to') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}
                            {!! Form::time('s_to', $product->s_to ?? null, ['class' => 'form-control']) !!}
                        </div>

                        <!-- Days of Week -->
                        <div class="form-group col-md-12 mb-3">
                            {!! Form::label('days_of_week', __('basicdata::models/db_products.fields.work_days') . ':', [
                                'class' => 'form-label fw-bold',
                            ]) !!}

                            <div class="row">
                                @foreach (config('week_days') as $key => $day)
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="form-check form-check-custom form-check-solid">
                                            {!! Form::checkbox('days_of_week[]', $key, in_array($key, old('days_of_week', $product->days_of_week ?? [])), [
                                                'class' => 'form-check-input',
                                                'id' => 'day_' . $key,
                                            ]) !!}
                                            {!! Form::label('day_' . $key, $day, ['class' => 'form-check-label']) !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $has_sizes = isset($product) && $product->sizes && count($product->sizes) > 0;
        @endphp
        <input type="hidden" name="have_sizes" id="have_sizes_input" value="{{ $has_sizes ? 1 : 0 }}">

        <!-- Accordion Item 5: Sizes -->
        <div class="accordion-item" id="sizes-accordion-item">
            <h2 class="accordion-header" id="headingSizes">
                <button class="accordion-button @if (!$has_sizes) collapsed @endif" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseSizes" aria-expanded="false"
                    aria-controls="collapseSizes">
                    {{ __('basicdata::models/db_products.sections.sizes') }}
                </button>
            </h2>
            <div id="collapseSizes" class="accordion-collapse collapse @if ($has_sizes) show @endif"
                aria-labelledby="headingSizes">
                <div class="accordion-body">
                    <div class="d-flex justify-content-end mb-3" id="add-size-button-container">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addSizeRow()">
                            <i class="fas fa-plus"></i> {{ __('basicdata::models/db_products.sections.add_size') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @foreach (config('langs') as $locale => $language)
                                        <th>{{ $language . ' ' . __('basicdata::models/db_products.size.name') }}</th>
                                    @endforeach
                                    <th>{{ __('basicdata::models/db_products.size.cost_price') }}</th>
                                    <th>{{ __('basicdata::models/db_products.size.sale_price') }}</th>
                                    <th>{{ __('basicdata::models/db_products.size.barcode') }}</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="sizes-container">
                                @if (isset($product) && $product->sizes && count($product->sizes) > 0)
                                    @foreach ($product->sizes as $index => $size)
                                        <tr class="size-row" data-index="{{ $index }}">
                                            @foreach (config('langs') as $locale => $language)
                                                <td>
                                                    {!! Form::text('sizes[' . $index . '][' . $locale . '][name]', $size->translate($locale)->name ?? null, [
                                                        'class' => 'form-control',
                                                        'placeholder' => __('basicdata::models/db_products.placeholders.size_name'),
                                                    ]) !!}
                                                </td>
                                            @endforeach
                                            <td>
                                                <div class="input-group">
                                                    <span
                                                        class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                    {!! Form::number('sizes[' . $index . '][cost_price]', $size->cost_price ?? null, [
                                                        'class' => 'form-control',
                                                        'step' => '0.01',
                                                        'min' => 0,
                                                        'placeholder' => '0.00',
                                                    ]) !!}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span
                                                        class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                    {!! Form::number('sizes[' . $index . '][sale_price]', $size->sale_price ?? null, [
                                                        'class' => 'form-control',
                                                        'step' => '0.01',
                                                        'min' => 0,
                                                        'placeholder' => '0.00',
                                                    ]) !!}
                                                </div>
                                            </td>
                                            <td>
                                                {!! Form::text('sizes[' . $index . '][barcode]', $size->barcode ?? null, [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('basicdata::models/db_products.placeholders.barcode'),
                                                ]) !!}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="removeSizeRow(this)">
                                                    <i class="fas fa-trash m-0"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="size-row" data-index="0">
                                        @foreach (config('langs') as $locale => $language)
                                            <td>
                                                {!! Form::text('sizes[0][' . $locale . '][name]', null, [
                                                    'class' => 'form-control',
                                                    'placeholder' => __('basicdata::models/db_products.placeholders.size_name'),
                                                ]) !!}
                                            </td>
                                        @endforeach
                                        <td>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                {!! Form::number('sizes[0][cost_price]', null, [
                                                    'class' => 'form-control',
                                                    'step' => '0.01',
                                                    'min' => 0,
                                                    'placeholder' => '0.00',
                                                ]) !!}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                {!! Form::number('sizes[0][sale_price]', null, [
                                                    'class' => 'form-control',
                                                    'step' => '0.01',
                                                    'min' => 0,
                                                    'placeholder' => '0.00',
                                                ]) !!}
                                            </div>
                                        </td>
                                        <td>
                                            {!! Form::text('sizes[0][barcode]', null, [
                                                'class' => 'form-control',
                                                'placeholder' => __('basicdata::models/db_products.placeholders.barcode'),
                                            ]) !!}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-primary btn-sm"
                                                onclick="removeSizeRow(this)">
                                                <i class="fas fa-trash m-0"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @php

        $translations = [
            'unit_id' => __('basicdata::models/db_products.unit.unit_id'),

            'conversion_factor' => __('basicdata::models/db_products.unit.conversion_factor'),

            'is_base' => __('basicdata::models/db_products.unit.is_base'),

            'remove' => '',

            'min_one_unit' => __('basicdata::models/db_products.messages.min_one_unit'),

            'size_name' => __('basicdata::models/db_products.size.name'),

            'cost_price' => __('basicdata::models/db_products.size.cost_price'),

            'sale_price' => __('basicdata::models/db_products.size.sale_price'),

            'barcode' => __('basicdata::models/db_products.size.barcode'),

            'size_name_placeholder' => __('basicdata::models/db_products.placeholders.size_name'),

            'barcode_placeholder' => __('basicdata::models/db_products.placeholders.barcode'),
        ];

    @endphp

    <script>
        // Pass data from PHP to JavaScript

        const translations = @json($translations);

        const availableUnits = @json($units);

        const availableLangs = @json(config('langs'));

        const currencySymbol = "{{ config('app.currency', 'SAR') }}";



        let unitIndex = {{ isset($product) && $product->units ? count($product->units) : 1 }};

        let sizeIndex = {{ isset($product) && $product->sizes ? count($product->sizes) : 1 }};






        function addUnitRow() {

            const container = document.getElementById('units-container');



            let unitOptions = ``;

            for (const id in availableUnits) {

                unitOptions += `<option value="${id}">${availableUnits[id]}</option>`;

            }



            const newRow = `

        <div class="unit-row mb-3 p-3 border rounded" data-index="${unitIndex}">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label class="form-label fw-bold">${translations.unit_id}:</label>

                    <select name="units[${unitIndex}][unit_id]" class="form-control select2-dynamic" placeholder="${translations.unit_id}">

                        ${unitOptions}

                    </select>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="form-label fw-bold">${translations.conversion_factor}:</label>

                    <input type="number" name="units[${unitIndex}][conversion_factor]" class="form-control" step="0.01" min="0" value="1" placeholder="1.00">

                </div>

                <div class="col-md-3 mb-3">

                    <label class="form-label fw-bold">${translations.is_base}</label>

                    <div class="form-check form-switch mt-2">

                        <input type="checkbox" name="units[${unitIndex}][is_base]" value="1" class="form-check-input is-base-checkbox" id="is_base_${unitIndex}">

                    </div>

                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">

                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="removeUnitRow(this)">

                        <i class="fas fa-trash"></i>

                    </button>

                </div>

            </div>

        </div>

    `;

            container.insertAdjacentHTML('beforeend', newRow);



            // Re-initialize select2 for the new row

            if (typeof $.fn.select2 !== 'undefined') {

                $newRow = $(container.lastElementChild);
                $newRow.find('.select2-dynamic').select2();
                $newRow.find('.is-base-checkbox').on('change', handleIsBaseChange);

            }

            unitIndex++;

        }



        function removeUnitRow(button) {

            const row = button.closest('.unit-row');

            const container = document.getElementById('units-container');

            if (container.children.length > 1) {

                row.remove();

            } else {

                alert(translations.min_one_unit);

            }

        }



        function addSizeRow() {

            const container = document.getElementById('sizes-container');



            let nameFields = '';

            for (const locale in availableLangs) {
                const language = availableLangs[locale];
                nameFields +=
                    `<td><input type="text" name="sizes[${sizeIndex}][${locale}][name]" class="form-control" placeholder="${translations.size_name_placeholder}"></td>`;
            }



            const newRow = `
            <tr class="size-row" data-index="${sizeIndex}">
                ${nameFields}
                <td>
                    <div class="input-group">
                        <span class="input-group-text">${currencySymbol}</span>
                        <input type="number" name="sizes[${sizeIndex}][cost_price]" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">${currencySymbol}</span>
                        <input type="number" name="sizes[${sizeIndex}][sale_price]" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                </td>
                <td>
                    <input type="text" name="sizes[${sizeIndex}][barcode]" class="form-control" placeholder="${translations.barcode_placeholder}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm" onclick="removeSizeRow(this)">
                        <i class="fas fa-trash m-0"></i>
                    </button>
                </td>
            </tr>
        `;
            container.insertAdjacentHTML('beforeend', newRow);
            sizeIndex++;
        }



        function removeSizeRow(button) {
            const row = button.closest('tr.size-row');
            row.remove();
        }



        document.addEventListener('DOMContentLoaded', function() {
            // Handle 'is_base' checkbox logic
            const isBaseCheckboxes = document.querySelectorAll('.is-base-checkbox');
            isBaseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', handleIsBaseChange);
            });

            // Ensure only one is checked on load
            const checkedBoxes = document.querySelectorAll('.is-base-checkbox:checked');
            if (checkedBoxes.length > 1) {
                checkedBoxes.forEach((cb, index) => {
                    if (index > 0) cb.checked = false;
                });
            }

            // On page load, if no 'is_base' is checked, check the first one.
            const anyBaseChecked = document.querySelector('.is-base-checkbox:checked');
            if (!anyBaseChecked) {
                const firstBaseCheckbox = document.querySelector('.is-base-checkbox');
                if (firstBaseCheckbox) firstBaseCheckbox.checked = true;
            }
            // Form submission validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const baseUnitsChecked = document.querySelectorAll('.is-base-checkbox:checked').length;
                    if (baseUnitsChecked === 0) {
                        e.preventDefault();
                        alert('{{ __('يجب تحديد وحدة أساسية واحدة على الأقل.') }}');
                    } else if (baseUnitsChecked > 1) {
                        e.preventDefault();
                        alert('{{ __('يجب تحديد وحدة أساسية واحدة فقط.') }}');
                    }
                });
            }

            // Logic for Sizes Accordion
            const collapseSizes = document.getElementById('collapseSizes');
            const haveSizesInput = document.getElementById('have_sizes_input');

            function toggleSizeInputs(disabled) {
                document.querySelectorAll('#sizes-container input').forEach(input => {
                    input.disabled = disabled;
                });
            }

            collapseSizes.addEventListener('show.bs.collapse', function() {
                haveSizesInput.value = 1;
                toggleSizeInputs(false);
            });

            collapseSizes.addEventListener('hide.bs.collapse', function() {
                haveSizesInput.value = 0;
                toggleSizeInputs(true);
            });
        });

        function handleIsBaseChange(event) {
            if (event.target.checked) {
                document.querySelectorAll('.is-base-checkbox').forEach(checkbox => {
                    if (checkbox !== event.target) {
                        checkbox.checked = false;
                    }
                });
            }
        }
    </script>
@endpush
