<div>
    @if($isOpen)
        <!-- Modal Backdrop with blur -->
        <div class="modal-backdrop fade show" style="z-index: 1050; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);" wire:click="closeModal"></div>

        <!-- Product Modal Dialog -->
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" aria-modal="true" role="dialog" x-data="{ activeTab: 'basic' }">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 960px;">
                <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: #ffffff;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-3 px-5 border-bottom d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 40px; height: 40px; background: {{ $type == 2 ? '#10b981' : '#2563eb' }}; color: #ffffff;">
                                <i class="{{ $type == 2 ? 'fa-solid fa-bell-concierge' : 'fa-solid fa-box-open' }} fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ $type == 2 ? __('basicdata::models/db_products.services') : __('basicdata::models/db_products.singular') }}
                                </h5>
                                <span class="text-muted fs-8">
                                    {{ $type == 2 ? 'بيانات الخدمة، المواعيد، والتسعير' : 'بيانات المنتج، المقاسات، الوحدات المتعددة، والتسعير' }}
                                </span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none" wire:click="closeModal" aria-label="Close" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Navigation Tabs Bar (تابات حديثة وأنيقة) -->
                    <div class="px-5 pt-3 border-bottom bg-white">
                        <ul class="nav nav-pills nav-pills-custom gap-2 border-0" role="tablist">
                            <!-- Tab 1: Basic Info -->
                            <li class="nav-item" role="presentation">
                                <a class="nav-link px-3 py-2 fs-7 fw-bold d-flex align-items-center gap-2 rounded-2 cursor-pointer border"
                                   :class="activeTab === 'basic' ? 'active bg-primary text-white border-primary shadow-xs' : 'bg-light text-gray-700 border-transparent hover-bg-light-primary'"
                                   @click="activeTab = 'basic'">
                                    <i class="fa-solid fa-circle-info fs-7" :class="activeTab === 'basic' ? 'text-white' : 'text-primary'"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.basic_info') }}</span>
                                </a>
                            </li>

                            <!-- Tab 2: Multiple Units -->
                            <li class="nav-item" role="presentation">
                                <a class="nav-link px-3 py-2 fs-7 fw-bold d-flex align-items-center gap-2 rounded-2 cursor-pointer border"
                                   :class="activeTab === 'units' ? 'active bg-primary text-white border-primary shadow-xs' : 'bg-light text-gray-700 border-transparent hover-bg-light-primary'"
                                   @click="activeTab = 'units'">
                                    <i class="fa-solid fa-layer-group fs-7" :class="activeTab === 'units' ? 'text-white' : 'text-success'"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.units') }}</span>
                                    <span class="badge rounded-pill ms-1" :class="activeTab === 'units' ? 'bg-white text-primary' : 'bg-secondary text-gray-700'">
                                        {{ count($units) }}
                                    </span>
                                </a>
                            </li>

                            <!-- Tab 3: Sizes & Variations -->
                            <li class="nav-item" role="presentation">
                                <a class="nav-link px-3 py-2 fs-7 fw-bold d-flex align-items-center gap-2 rounded-2 cursor-pointer border"
                                   :class="activeTab === 'sizes' ? 'active bg-primary text-white border-primary shadow-xs' : 'bg-light text-gray-700 border-transparent hover-bg-light-primary'"
                                   @click="activeTab = 'sizes'">
                                    <i class="fa-solid fa-ruler-combined fs-7" :class="activeTab === 'sizes' ? 'text-white' : 'text-danger'"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.sizes') }}</span>
                                    @if($have_sizes)
                                        <span class="badge rounded-pill ms-1" :class="activeTab === 'sizes' ? 'bg-white text-primary' : 'bg-danger text-white'">
                                            {{ count($sizes) }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <!-- Tab 4: VAT & Kitchen -->
                            <li class="nav-item" role="presentation">
                                <a class="nav-link px-3 py-2 fs-7 fw-bold d-flex align-items-center gap-2 rounded-2 cursor-pointer border"
                                   :class="activeTab === 'vat' ? 'active bg-primary text-white border-primary shadow-xs' : 'bg-light text-gray-700 border-transparent hover-bg-light-primary'"
                                   @click="activeTab = 'vat'">
                                    <i class="fa-solid fa-receipt fs-7" :class="activeTab === 'vat' ? 'text-white' : 'text-warning'"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.vat_info') }}</span>
                                </a>
                            </li>

                            <!-- Tab 5: Other Info & Schedule -->
                            <li class="nav-item" role="presentation">
                                <a class="nav-link px-3 py-2 fs-7 fw-bold d-flex align-items-center gap-2 rounded-2 cursor-pointer border"
                                   :class="activeTab === 'other' ? 'active bg-primary text-white border-primary shadow-xs' : 'bg-light text-gray-700 border-transparent hover-bg-light-primary'"
                                   @click="activeTab = 'other'">
                                    <i class="fa-solid fa-clock fs-7" :class="activeTab === 'other' ? 'text-white' : 'text-info'"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.other_info') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Modal Form -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-5" style="max-height: calc(78vh - 160px); min-height: 380px; overflow-y: auto; background-color: #fafbfc;">
                            
                            @if ($errors->any())
                                <div class="alert alert-danger d-flex align-items-center p-3 mb-4 rounded-3 border-0 shadow-sm" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-danger fw-bold fs-7"><i class="fa-solid fa-circle-exclamation me-2"></i>يرجى مراجعة الحقول التالية:</h6>
                                        <ul class="mb-0 ps-3 fs-8 text-danger">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- TAB 1: BASIC INFO -->
                            <div x-show="activeTab === 'basic'" x-transition.opacity.duration.200ms>
                                <div class="bg-white p-4 rounded-3 border shadow-xs">
                                    <div class="row g-3">
                                        <!-- Multilingual Name Fields -->
                                        @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                    {{ $language }} - {{ __('basicdata::models/db_products.fields.name') }} <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       wire:model="name.{{ $locale }}" 
                                                       class="form-control form-control-solid fs-7 @error('name.'.$locale) is-invalid @enderror" 
                                                       placeholder="{{ __('basicdata::models/db_products.placeholders.name') }} ({{ $language }})" />
                                                @error('name.'.$locale)
                                                    <div class="invalid-feedback fs-8">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach

                                        <!-- Barcode -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                {{ __('basicdata::models/db_products.fields.barcode') }}
                                            </label>
                                            <div class="input-group input-group-solid">
                                                <span class="input-group-text bg-light text-muted border-0"><i class="fa-solid fa-barcode"></i></span>
                                                <input type="text" 
                                                       wire:model="barcode" 
                                                       class="form-control form-control-solid fs-7 @error('barcode') is-invalid @enderror" 
                                                       placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                                            </div>
                                            @error('barcode') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Category -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                {{ __('basicdata::models/db_products.fields.category_id') }} <span class="text-danger">*</span>
                                            </label>
                                            <select wire:model="category_id" class="form-select form-select-solid fs-7 @error('category_id') is-invalid @enderror">
                                                <option value="">-- @lang('basicdata::lang.select') --</option>
                                                @foreach($categories as $catId => $catName)
                                                    <option value="{{ $catId }}">{{ $catName }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Sale Price -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                {{ __('basicdata::models/db_products.fields.prod_price') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-solid">
                                                <span class="input-group-text bg-light text-primary fw-bold border-0">{{ config('app.currency', 'SAR') }}</span>
                                                <input type="number" 
                                                       step="0.01" 
                                                       min="0" 
                                                       wire:model="prod_price" 
                                                       class="form-control form-control-solid fs-7 @error('prod_price') is-invalid @enderror" 
                                                       placeholder="0.00" />
                                            </div>
                                            @error('prod_price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Cost Price -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                {{ __('basicdata::models/db_products.fields.cost_price') }}
                                            </label>
                                            <div class="input-group input-group-solid">
                                                <span class="input-group-text bg-light text-muted border-0">{{ config('app.currency', 'SAR') }}</span>
                                                <input type="number" 
                                                       step="0.01" 
                                                       min="0" 
                                                       wire:model="cost_price" 
                                                       class="form-control form-control-solid fs-7 @error('cost_price') is-invalid @enderror" 
                                                       placeholder="0.00" />
                                            </div>
                                            @error('cost_price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-2 d-block">
                                                {{ __('basicdata::models/db_products.fields.status') }}
                                            </label>
                                            <div class="d-flex align-items-center gap-4 mt-1">
                                                @foreach ($statuses as $value => $label)
                                                    <label class="d-flex align-items-center gap-2 cursor-pointer p-2 px-3 rounded-2 border {{ (int)$status === (int)$value ? 'bg-light-primary border-primary text-primary fw-bold' : 'bg-light border-light text-muted' }}" style="transition: all 0.2s;">
                                                        <input class="form-check-input mt-0" type="radio" wire:model="status" id="status_{{ $value }}" value="{{ $value }}">
                                                        <span class="fs-7">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Image -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                {{ __('basicdata::models/db_products.fields.img') }}
                                            </label>
                                            <div class="d-flex align-items-center gap-3">
                                                <input type="file" wire:model="img" class="form-control form-control-solid fs-7" accept="image/*" />
                                                
                                                @if ($img)
                                                    <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0">
                                                        <img src="{{ $img->temporaryUrl() }}" alt="Preview" class="object-fit-cover w-45px h-45px">
                                                    </div>
                                                @elseif ($existing_img)
                                                    <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0">
                                                        <img src="{{ $existing_img }}" alt="Image" class="object-fit-cover w-45px h-45px">
                                                    </div>
                                                @endif
                                            </div>
                                            @error('img') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: MULTIPLE UNITS -->
                            <div x-show="activeTab === 'units'" x-transition.opacity.duration.200ms style="display: none;">
                                <div class="bg-white p-4 rounded-3 border shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="fw-bold text-gray-900 mb-1 fs-7">الوحدات ومعاملات التحويل</h6>
                                            <span class="text-muted fs-8">أضف وحدات قياس متعددة مع تحديد معامل التحويل للوحدة الأساسية</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold" wire:click="addUnitRow">
                                            <i class="fas fa-plus fs-8 me-1"></i> {{ __('basicdata::models/db_products.sections.add_unit') }}
                                        </button>
                                    </div>

                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($units as $index => $unit)
                                            <div class="p-3 border rounded-3 bg-light-subtle d-flex flex-wrap align-items-center justify-content-between gap-3" wire:key="unit-row-{{ $index }}">
                                                <div class="row g-3 flex-grow-1 align-items-center">
                                                    <div class="col-md-5">
                                                        <label class="form-label fw-bold fs-8 text-gray-700 mb-1">{{ __('basicdata::models/db_products.unit.unit_id') }}:</label>
                                                        <select wire:model="units.{{ $index }}.unit_id" class="form-select form-select-solid form-select-sm fs-7">
                                                            <option value="">-- @lang('basicdata::lang.select') --</option>
                                                            @foreach($unitsList as $uId => $uName)
                                                                <option value="{{ $uId }}">{{ $uName }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold fs-8 text-gray-700 mb-1">{{ __('basicdata::models/db_products.unit.conversion_factor') }}:</label>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="0" 
                                                               wire:model="units.{{ $index }}.conversion_factor" 
                                                               class="form-control form-control-solid form-control-sm fs-7" 
                                                               placeholder="1.00" />
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label fw-bold fs-8 text-gray-700 mb-1">{{ __('basicdata::models/db_products.unit.is_base') }}:</label>
                                                        <div class="form-check form-switch mt-1">
                                                            <input class="form-check-input" type="checkbox" wire:model="units.{{ $index }}.is_base" value="1" id="is_base_{{ $index }}">
                                                            <label class="form-check-label fs-8 text-gray-700 fw-semibold" for="is_base_{{ $index }}">وحدة أساسية</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    @if(count($units) > 1)
                                                        <button type="button" class="btn btn-icon btn-sm btn-light-danger rounded-circle" wire:click="removeUnitRow({{ $index }})" title="حذف الوحدة">
                                                            <i class="fas fa-trash-can fs-8"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: SIZES & VARIATIONS -->
                            <div x-show="activeTab === 'sizes'" x-transition.opacity.duration.200ms style="display: none;">
                                <div class="bg-white p-4 rounded-3 border shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" wire:model.live="have_sizes" id="have_sizes_switch">
                                            <label class="form-check-label fw-bold fs-7 text-gray-900" for="have_sizes_switch">تفعيل المقاسات والتنويعات (Sizes & Variations)</label>
                                        </div>
                                        @if($have_sizes)
                                            <button type="button" class="btn btn-sm btn-light-primary font-weight-bold" wire:click="addSizeRow">
                                                <i class="fas fa-plus fs-8 me-1"></i> {{ __('basicdata::models/db_products.sections.add_size') }}
                                            </button>
                                        @endif
                                    </div>

                                    @if($have_sizes)
                                        <div class="table-responsive border rounded-3 overflow-hidden">
                                            <table class="table table-row-dashed table-row-gray-200 align-middle gs-4 gy-3 mb-0">
                                                <thead class="bg-light">
                                                    <tr class="text-start text-gray-700 fw-bold fs-8 text-uppercase">
                                                        @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                                            <th>{{ $language . ' - ' . __('basicdata::models/db_products.size.name') }}</th>
                                                        @endforeach
                                                        <th>{{ __('basicdata::models/db_products.size.cost_price') }}</th>
                                                        <th>{{ __('basicdata::models/db_products.size.sale_price') }}</th>
                                                        <th>{{ __('basicdata::models/db_products.size.barcode') }}</th>
                                                        <th style="width: 40px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sizes as $index => $size)
                                                        <tr wire:key="size-row-{{ $index }}">
                                                            @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                                                <td>
                                                                    <input type="text" 
                                                                           wire:model="sizes.{{ $index }}.{{ $locale }}.name" 
                                                                           class="form-control form-control-solid form-control-sm fs-8" 
                                                                           placeholder="{{ __('basicdata::models/db_products.placeholders.size_name') }}" />
                                                                </td>
                                                            @endforeach
                                                            <td>
                                                                <div class="input-group input-group-solid input-group-sm">
                                                                    <span class="input-group-text bg-light text-muted border-0 fs-8">{{ config('app.currency', 'SAR') }}</span>
                                                                    <input type="number" 
                                                                           step="0.01" 
                                                                           min="0" 
                                                                           wire:model="sizes.{{ $index }}.cost_price" 
                                                                           class="form-control form-control-solid form-control-sm fs-8" 
                                                                           placeholder="0.00" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group input-group-solid input-group-sm">
                                                                    <span class="input-group-text bg-light text-primary fw-bold border-0 fs-8">{{ config('app.currency', 'SAR') }}</span>
                                                                    <input type="number" 
                                                                           step="0.01" 
                                                                           min="0" 
                                                                           wire:model="sizes.{{ $index }}.sale_price" 
                                                                           class="form-control form-control-solid form-control-sm fs-8" 
                                                                           placeholder="0.00" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" 
                                                                       wire:model="sizes.{{ $index }}.barcode" 
                                                                       class="form-control form-control-solid form-control-sm fs-8" 
                                                                       placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-icon btn-sm btn-light-danger rounded-circle" wire:click="removeSizeRow({{ $index }})" title="حذف المقاس">
                                                                    <i class="fas fa-trash-can fs-8"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-ruler-combined fs-1 mb-2 text-gray-400 d-block"></i>
                                            <p class="fs-7 mb-0">قم بتفعيل خيار المقاسات لإضافة أحجام متعددة للصنف بأسعار وباركودات مختلفة.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- TAB 4: VAT & KITCHEN -->
                            <div x-show="activeTab === 'vat'" x-transition.opacity.duration.200ms style="display: none;">
                                <div class="bg-white p-4 rounded-3 border shadow-xs">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">{{ __('basicdata::models/db_products.fields.vat') }}:</label>
                                            <select wire:model="tax_id" class="form-select form-select-solid fs-7">
                                                <option value="">-- @lang('basicdata::lang.select') --</option>
                                                @foreach($vats as $vId => $vName)
                                                    <option value="{{ $vId }}">{{ $vName }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-muted fs-8 mt-1 d-block">حساب الضريبة المطبق على عمليات بيع هذا الصنف.</span>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">{{ __('basicdata::models/db_products.fields.kitchen_id') }}:</label>
                                            <select wire:model="kitchen_id" class="form-select form-select-solid fs-7">
                                                <option value="">-- @lang('basicdata::lang.select') --</option>
                                                @foreach($kitchens as $kId => $kName)
                                                    <option value="{{ $kId }}">{{ $kName }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-muted fs-8 mt-1 d-block">المطبخ أو قسم التحضير الذي تُطبع أو تُرسل إليه الطلبات.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: OTHER INFO & SCHEDULE -->
                            <div x-show="activeTab === 'other'" x-transition.opacity.duration.200ms style="display: none;">
                                <div class="bg-white p-4 rounded-3 border shadow-xs">
                                    <div class="row g-3">
                                        @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1">
                                                    {{ $language }} - {{ __('basicdata::models/db_products.fields.details') }}:
                                                </label>
                                                <textarea wire:model="details.{{ $locale }}" 
                                                          class="form-control form-control-solid fs-7" 
                                                          rows="3" 
                                                          placeholder="{{ __('basicdata::models/db_products.placeholders.details') }} ({{ $language }})"></textarea>
                                            </div>
                                        @endforeach

                                        @if ($type != 2)
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold fs-7 text-gray-700 mb-1">{{ __('basicdata::models/db_products.fields.min_quantity') }}:</label>
                                                <input type="number" 
                                                       step="0.01" 
                                                       min="0" 
                                                       wire:model="min_quantity" 
                                                       class="form-control form-control-solid fs-7" 
                                                       placeholder="0.00" />
                                            </div>
                                        @endif

                                        <div class="col-md-3">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">{{ __('basicdata::models/db_products.fields.s_from') }}:</label>
                                            <input type="time" wire:model="s_from" class="form-control form-control-solid fs-7" />
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-1">{{ __('basicdata::models/db_products.fields.s_to') }}:</label>
                                            <input type="time" wire:model="s_to" class="form-control form-control-solid fs-7" />
                                        </div>

                                        <!-- Days of Week -->
                                        <div class="col-md-12 mt-3">
                                            <label class="form-label fw-bold fs-7 text-gray-700 mb-2">{{ __('basicdata::models/db_products.fields.work_days') }}:</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach (config('week_days', [
                                                    'sat' => 'السبت',
                                                    'sun' => 'الأحد',
                                                    'mon' => 'الاثنين',
                                                    'tue' => 'الثلاثاء',
                                                    'wed' => 'الأربعاء',
                                                    'thu' => 'الخميس',
                                                    'fri' => 'الجمعة',
                                                ]) as $key => $day)
                                                    <label class="d-flex align-items-center gap-2 p-2 px-3 rounded-2 border bg-light cursor-pointer hover-bg-light-primary" style="transition: all 0.2s;">
                                                        <input class="form-check-input mt-0" type="checkbox" wire:model="work_days" value="{{ $key }}" id="day_{{ $key }}">
                                                        <span class="fs-8 text-gray-800 fw-medium">{{ $day }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer py-3 px-5 border-top d-flex justify-content-between align-items-center bg-white">
                            <button type="button" class="btn btn-sm btn-light fs-7 px-4" wire:click="closeModal">
                                <i class="fa-solid fa-xmark fs-8 me-1"></i>
                                @lang('crud.cancel')
                            </button>
                            <button type="submit" class="btn btn-sm front-btn-primary fs-7 px-5 shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fa-solid fa-check fs-8 me-1"></i>
                                    @lang('crud.save')
                                </span>
                                <span wire:loading>
                                    <i class="fa-solid fa-spinner fa-spin fs-8 me-1"></i>
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
