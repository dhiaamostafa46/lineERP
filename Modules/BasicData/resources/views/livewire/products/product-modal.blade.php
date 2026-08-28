<div>
    @if($isOpen)
        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show" style="z-index: 1050;" wire:click="closeModal"></div>

        <!-- Product Modal Dialog -->
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-2xl rounded-4" style="background: #ffffff;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between bg-light">
                        <h5 class="modal-title fw-bold text-gray-900 fs-4">
                            @if($is_edit)
                                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>
                                {{ __('crud.edit') }} {{ $type == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.singular') }}
                            @else
                                <i class="fa-solid fa-circle-plus text-primary me-2"></i>
                                {{ __('crud.add_new') }} {{ $type == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.singular') }}
                            @endif
                        </h5>
                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" wire:click="closeModal" aria-label="Close">
                            <i class="fa-solid fa-xmark fs-4"></i>
                        </button>
                    </div>

                    <!-- Modal Form -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
                            
                            @if ($errors->any())
                                <div class="alert alert-danger p-3 mb-4 rounded-3">
                                    <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-2"></i>يرجى تصحيح الأخطاء التالية:</div>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="accordion" id="livewireProductAccordion">
                                
                                <!-- Accordion Item 1: Basic Info -->
                                <div class="accordion-item border rounded-3 mb-3 shadow-none">
                                    <h2 class="accordion-header" id="headingBasicInfo">
                                        <button class="accordion-button bg-light fw-bold text-gray-800" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasicInfo" aria-expanded="true">
                                            <i class="fa-solid fa-circle-info text-primary me-2"></i>
                                            {{ __('basicdata::models/db_products.sections.basic_info') }}
                                        </button>
                                    </h2>
                                    <div id="collapseBasicInfo" class="accordion-collapse collapse show">
                                        <div class="accordion-body p-4">
                                            <div class="row">
                                                <!-- Multilingual Name Fields -->
                                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label class="form-label fw-bold">
                                                            {{ $language }} {{ __('basicdata::models/db_products.fields.name') }}: <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" 
                                                               wire:model="name.{{ $locale }}" 
                                                               class="form-control @error('name.'.$locale) is-invalid @enderror" 
                                                               placeholder="{{ __('basicdata::models/db_products.placeholders.name') }}" />
                                                        @error('name.'.$locale)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endforeach

                                                <!-- Barcode -->
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.barcode') }}:</label>
                                                    <input type="text" 
                                                           wire:model="barcode" 
                                                           class="form-control @error('barcode') is-invalid @enderror" 
                                                           placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                                                    @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <!-- Category -->
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.category_id') }}: <span class="text-danger">*</span></label>
                                                    <select wire:model="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                                        @foreach($categories as $catId => $catName)
                                                            <option value="{{ $catId }}">{{ $catName }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <!-- Sale Price -->
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.prod_price') }}: <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="0" 
                                                               wire:model="prod_price" 
                                                               class="form-control @error('prod_price') is-invalid @enderror" 
                                                               placeholder="0.00" />
                                                    </div>
                                                    @error('prod_price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                                </div>

                                                <!-- Cost Price -->
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.cost_price') }}:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="0" 
                                                               wire:model="cost_price" 
                                                               class="form-control @error('cost_price') is-invalid @enderror" 
                                                               placeholder="0.00" />
                                                    </div>
                                                    @error('cost_price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                                </div>

                                                <!-- Status -->
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold d-block">{{ __('basicdata::models/db_products.fields.status') }}:</label>
                                                    <div class="d-flex gap-4 mt-2">
                                                        @foreach ($statuses as $value => $label)
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" wire:model="status" id="status_{{ $value }}" value="{{ $value }}">
                                                                <label class="form-check-label" for="status_{{ $value }}">{{ $label }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Image -->
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.img') }}:</label>
                                                    <input type="file" wire:model="img" class="form-control" accept="image/*" />
                                                    @error('img') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                                    
                                                    <div class="mt-2">
                                                        @if ($img)
                                                            <img src="{{ $img->temporaryUrl() }}" alt="Product Image" class="img-thumbnail" style="max-height: 100px;">
                                                        @elseif ($existing_img)
                                                            <img src="{{ $existing_img }}" alt="Product Image" class="img-thumbnail" style="max-height: 100px;">
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion Item 2: Units -->
                                <div class="accordion-item border rounded-3 mb-3 shadow-none">
                                    <h2 class="accordion-header" id="headingUnits">
                                        <button class="accordion-button bg-light fw-bold text-gray-800" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnits" aria-expanded="true">
                                            <i class="fa-solid fa-layer-group text-primary me-2"></i>
                                            {{ __('basicdata::models/db_products.sections.units') }}
                                        </button>
                                    </h2>
                                    <div id="collapseUnits" class="accordion-collapse collapse show">
                                        <div class="accordion-body p-4">
                                            <div class="d-flex justify-content-end mb-3">
                                                <button type="button" class="btn btn-primary btn-sm" wire:click="addUnitRow">
                                                    <i class="fas fa-plus me-1"></i> {{ __('basicdata::models/db_products.sections.add_unit') }}
                                                </button>
                                            </div>

                                            <div id="units-container">
                                                @foreach ($units as $index => $unit)
                                                    <div class="unit-row mb-3 p-3 border rounded-3 bg-light-subtle" wire:key="unit-row-{{ $index }}">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-4 mb-2">
                                                                <label class="form-label fw-bold">{{ __('basicdata::models/db_products.unit.unit_id') }}:</label>
                                                                <select wire:model="units.{{ $index }}.unit_id" class="form-select">
                                                                    <option value="">-- @lang('basicdata::lang.select') --</option>
                                                                    @foreach($unitsList as $uId => $uName)
                                                                        <option value="{{ $uId }}">{{ $uName }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label fw-bold">{{ __('basicdata::models/db_products.unit.conversion_factor') }}:</label>
                                                                <input type="number" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       wire:model="units.{{ $index }}.conversion_factor" 
                                                                       class="form-control" 
                                                                       placeholder="1.00" />
                                                            </div>

                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label fw-bold">{{ __('basicdata::models/db_products.unit.is_base') }}:</label>
                                                                <div class="form-check form-switch mt-2">
                                                                    <input class="form-check-input" type="checkbox" wire:model="units.{{ $index }}.is_base" value="1" id="is_base_{{ $index }}">
                                                                    <label class="form-check-label" for="is_base_{{ $index }}">وحدة أساسية</label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-2 mb-2 d-flex align-items-end">
                                                                @if(count($units) > 1)
                                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" wire:click="removeUnitRow({{ $index }})">
                                                                        <i class="fas fa-trash me-1"></i> حذف
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion Item 3: VAT Info & Kitchen -->
                                <div class="accordion-item border rounded-3 mb-3 shadow-none">
                                    <h2 class="accordion-header" id="headingvatInfo">
                                        <button class="accordion-button collapsed bg-light fw-bold text-gray-800" type="button" data-bs-toggle="collapse" data-bs-target="#collapsevatInfo" aria-expanded="false">
                                            <i class="fa-solid fa-receipt text-primary me-2"></i>
                                            {{ __('basicdata::models/db_products.sections.vat_info') }}
                                        </button>
                                    </h2>
                                    <div id="collapsevatInfo" class="accordion-collapse collapse">
                                        <div class="accordion-body p-4">
                                            <div class="row">
                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.vat') }}:</label>
                                                    <select wire:model="tax_id" class="form-select">
                                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                                        @foreach($vats as $vId => $vName)
                                                            <option value="{{ $vId }}">{{ $vName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-6 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.kitchen_id') }}:</label>
                                                    <select wire:model="kitchen_id" class="form-select">
                                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                                        @foreach($kitchens as $kId => $kName)
                                                            <option value="{{ $kId }}">{{ $kName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion Item 4: Other Info & Schedule -->
                                <div class="accordion-item border rounded-3 mb-3 shadow-none">
                                    <h2 class="accordion-header" id="headingDateInfo">
                                        <button class="accordion-button collapsed bg-light fw-bold text-gray-800" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDateInfo" aria-expanded="false">
                                            <i class="fa-solid fa-clock text-primary me-2"></i>
                                            {{ __('basicdata::models/db_products.sections.other_info') }}
                                        </button>
                                    </h2>
                                    <div id="collapseDateInfo" class="accordion-collapse collapse">
                                        <div class="accordion-body p-4">
                                            <div class="row">
                                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label class="form-label fw-bold">
                                                            {{ $language }} {{ __('basicdata::models/db_products.fields.details') }}:
                                                        </label>
                                                        <textarea wire:model="details.{{ $locale }}" 
                                                                  class="form-control" 
                                                                  rows="3" 
                                                                  placeholder="{{ __('basicdata::models/db_products.placeholders.details') }}"></textarea>
                                                    </div>
                                                @endforeach

                                                @if ($type != 2)
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.min_quantity') }}:</label>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="0" 
                                                               wire:model="min_quantity" 
                                                               class="form-control" 
                                                               placeholder="0.00" />
                                                    </div>
                                                @endif

                                                <div class="form-group col-md-3 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.s_from') }}:</label>
                                                    <input type="time" wire:model="s_from" class="form-control" />
                                                </div>

                                                <div class="form-group col-md-3 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.s_to') }}:</label>
                                                    <input type="time" wire:model="s_to" class="form-control" />
                                                </div>

                                                <!-- Days of Week -->
                                                <div class="form-group col-md-12 mb-3">
                                                    <label class="form-label fw-bold">{{ __('basicdata::models/db_products.fields.work_days') }}:</label>
                                                    <div class="row">
                                                        @foreach (config('week_days', [
                                                            'sat' => 'السبت',
                                                            'sun' => 'الأحد',
                                                            'mon' => 'الاثنين',
                                                            'tue' => 'الثلاثاء',
                                                            'wed' => 'الأربعاء',
                                                            'thu' => 'الخميس',
                                                            'fri' => 'الجمعة',
                                                        ]) as $key => $day)
                                                            <div class="col-md-3 col-6 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" wire:model="work_days" value="{{ $key }}" id="day_{{ $key }}">
                                                                    <label class="form-check-label" for="day_{{ $key }}">{{ $day }}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accordion Item 5: Sizes -->
                                <div class="accordion-item border rounded-3 mb-3 shadow-none">
                                    <h2 class="accordion-header" id="headingSizes">
                                        <button class="accordion-button collapsed bg-light fw-bold text-gray-800" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSizes" aria-expanded="false">
                                            <i class="fa-solid fa-ruler-combined text-primary me-2"></i>
                                            {{ __('basicdata::models/db_products.sections.sizes') }}
                                        </button>
                                    </h2>
                                    <div id="collapseSizes" class="accordion-collapse collapse">
                                        <div class="accordion-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" wire:model.live="have_sizes" id="have_sizes_switch">
                                                    <label class="form-check-label fw-bold" for="have_sizes_switch">تفعيل المقاسات والتنويعات</label>
                                                </div>
                                                @if($have_sizes)
                                                    <button type="button" class="btn btn-primary btn-sm" wire:click="addSizeRow">
                                                        <i class="fas fa-plus me-1"></i> {{ __('basicdata::models/db_products.sections.add_size') }}
                                                    </button>
                                                @endif
                                            </div>

                                            @if($have_sizes)
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                                                    <th>{{ $language . ' ' . __('basicdata::models/db_products.size.name') }}</th>
                                                                @endforeach
                                                                <th>{{ __('basicdata::models/db_products.size.cost_price') }}</th>
                                                                <th>{{ __('basicdata::models/db_products.size.sale_price') }}</th>
                                                                <th>{{ __('basicdata::models/db_products.size.barcode') }}</th>
                                                                <th style="width: 50px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sizes as $index => $size)
                                                                <tr wire:key="size-row-{{ $index }}">
                                                                    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                                                        <td>
                                                                            <input type="text" 
                                                                                   wire:model="sizes.{{ $index }}.{{ $locale }}.name" 
                                                                                   class="form-control form-control-sm" 
                                                                                   placeholder="{{ __('basicdata::models/db_products.placeholders.size_name') }}" />
                                                                        </td>
                                                                    @endforeach
                                                                    <td>
                                                                        <div class="input-group input-group-sm">
                                                                            <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                                            <input type="number" 
                                                                                   step="0.01" 
                                                                                   min="0" 
                                                                                   wire:model="sizes.{{ $index }}.cost_price" 
                                                                                   class="form-control form-control-sm" 
                                                                                   placeholder="0.00" />
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group input-group-sm">
                                                                            <span class="input-group-text">{{ config('app.currency', 'SAR') }}</span>
                                                                            <input type="number" 
                                                                                   step="0.01" 
                                                                                   min="0" 
                                                                                   wire:model="sizes.{{ $index }}.sale_price" 
                                                                                   class="form-control form-control-sm" 
                                                                                   placeholder="0.00" />
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" 
                                                                               wire:model="sizes.{{ $index }}.barcode" 
                                                                               class="form-control form-control-sm" 
                                                                               placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2" wire:click="removeSizeRow({{ $index }})">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-top py-3 px-5 d-flex justify-content-between bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="closeModal">
                                <i class="fa-solid fa-xmark me-1"></i>
                                @lang('crud.cancel')
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fa-solid fa-check me-1"></i>
                                    @lang('crud.save')
                                </span>
                                <span wire:loading>
                                    <i class="fa-solid fa-spinner fa-spin me-1"></i>
                                    جاري الحفظ...
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
