<div>
    @if($isOpen)
        @include('basicdata::layouts.partials._modal_styles')

        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show premium-modal-backdrop" wire:click="closeModal"></div>

        <!-- Product / Service Modal Dialog -->
        <div class="modal fade show d-block premium-modal-dialog" tabindex="-1" aria-modal="true" role="dialog" x-data="{ activeTab: 'basic' }">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 900px;">
                <div class="modal-content premium-modal-content overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" 
                                 style="width: 44px; height: 44px; background: {{ $type == 2 ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)' }}; color: #ffffff;">
                                <i class="{{ $type == 2 ? 'fa-solid fa-bell-concierge' : 'fa-solid fa-box-open' }} fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ $type == 2 ? __('basicdata::models/db_products.service') : __('basicdata::models/db_products.product') }}
                                </h4>
                                <span class="text-muted fs-8">
                                    {{ $type == 2 ? 'تعريف وتخصيص بيانات الخدمة وأسعارها' : 'إدارة بيانات المنتجات والوحدات والمقاسات' }}
                                </span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none hover-scale" wire:click="closeModal" aria-label="Close" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Navigation Segmented Tabs (تخصيص التابات بحسب النوع) -->
                    <div class="px-6 py-3 border-bottom" style="background: #f8fafc;">
                        <div class="segmented-tabs-wrapper w-100 justify-content-start">
                            @if($type == 2)
                                <!-- SERVICE TABS (Only 2 Tabs) -->
                                <button type="button" 
                                        class="segmented-tab-btn flex-fill justify-content-center" 
                                        :class="activeTab === 'basic' ? 'active text-success' : ''" 
                                        @click="activeTab = 'basic'">
                                    <i class="fa-solid fa-bell-concierge fs-7"></i>
                                    <span>بيانات وأسعار الخدمة</span>
                                </button>

                                <button type="button" 
                                        class="segmented-tab-btn flex-fill justify-content-center" 
                                        :class="activeTab === 'details' ? 'active text-success' : ''" 
                                        @click="activeTab = 'details'">
                                    <i class="fa-solid fa-align-left fs-7"></i>
                                    <span>تفاصيل ووصف الخدمة</span>
                                </button>
                            @else
                                <!-- PRODUCT TABS (3 Tabs) -->
                                <button type="button" 
                                        class="segmented-tab-btn flex-fill justify-content-center" 
                                        :class="activeTab === 'basic' ? 'active text-primary' : ''" 
                                        @click="activeTab = 'basic'">
                                    <i class="fa-solid fa-circle-info fs-7"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.basic_info') }}</span>
                                </button>

                                <button type="button" 
                                        class="segmented-tab-btn flex-fill justify-content-center" 
                                        :class="activeTab === 'units' ? 'active text-success' : ''" 
                                        @click="activeTab = 'units'">
                                    <i class="fa-solid fa-layer-group fs-7"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.units') }}</span>
                                    <span class="badge rounded-pill bg-light-success text-success fs-9 ms-1" style="padding: 0.2rem 0.5rem;">
                                        {{ count($units) }}
                                    </span>
                                </button>

                                <button type="button" 
                                        class="segmented-tab-btn flex-fill justify-content-center" 
                                        :class="activeTab === 'sizes' ? 'active text-danger' : ''" 
                                        @click="activeTab = 'sizes'">
                                    <i class="fa-solid fa-ruler-combined fs-7"></i>
                                    <span>{{ __('basicdata::models/db_products.sections.sizes') }}</span>
                                    @if($have_sizes)
                                        <span class="badge rounded-pill bg-danger text-white fs-9 ms-1" style="padding: 0.2rem 0.5rem;">
                                            {{ count($sizes) }}
                                        </span>
                                    @endif
                                </button>

                                <button type="button" 
                                        class="segmented-tab-btn flex-fill justify-content-center" 
                                        :class="activeTab === 'details' ? 'active text-primary' : ''" 
                                        @click="activeTab = 'details'">
                                    <i class="fa-solid fa-align-left fs-7"></i>
                                    <span>الوصف</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Modal Form -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-6" style="max-height: calc(75vh - 140px); min-height: 380px; overflow-y: auto; background-color: #ffffff;">
                            
                            @if ($errors->any())
                                <div class="alert alert-danger d-flex align-items-center p-3 mb-4 rounded-3 border-0 shadow-sm" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-danger fw-bold fs-7"><i class="fa-solid fa-circle-exclamation me-2"></i>@lang('crud.error'):</h6>
                                        <ul class="mb-0 ps-3 fs-8 text-danger">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- TAB 1: BASIC INFO -->
                            <div x-show="activeTab === 'basic'" x-transition.opacity.duration.150ms>
                                <div class="row g-4">
                                    <!-- Multilingual Name Fields -->
                                    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                        <div class="col-md-6">
                                            <label class="modern-form-label">
                                                {{ $language }} - {{ $type == 2 ? 'اسم الخدمة' : __('basicdata::models/db_products.fields.name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="name.{{ $locale }}" 
                                                   class="form-control modern-input @error('name.'.$locale) is-invalid @enderror" 
                                                   placeholder="{{ $type == 2 ? 'اسم الخدمة' : __('basicdata::models/db_products.placeholders.name') }} ({{ $language }})" />
                                            @error('name.'.$locale)
                                                <div class="invalid-feedback fs-8">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach

                                    @if($type != 2)
                                        <!-- Barcode (Products Only) -->
                                        <div class="col-md-6">
                                            <label class="modern-form-label">
                                                {{ __('basicdata::models/db_products.fields.barcode') }}
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted border" style="border-radius: 0 0.625rem 0.625rem 0;"><i class="fa-solid fa-barcode"></i></span>
                                                <input type="text" 
                                                       wire:model="barcode" 
                                                       class="form-control modern-input @error('barcode') is-invalid @enderror" 
                                                       style="border-radius: 0.625rem 0 0 0.625rem;"
                                                       placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                                            </div>
                                            @error('barcode') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                                        </div>
                                    @endif

                                    <!-- Category -->
                                    <div class="col-md-6">
                                        <label class="modern-form-label">
                                            {{ __('basicdata::models/db_products.fields.category_id') }} <span class="text-danger">*</span>
                                        </label>
                                        <select wire:model="category_id" class="form-select modern-input @error('category_id') is-invalid @enderror">
                                            <option value="">-- @lang('basicdata::lang.select') --</option>
                                            @foreach($categories as $catId => $catName)
                                                <option value="{{ $catId }}">{{ $catName }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Sale Price -->
                                    <div class="col-md-6">
                                        <label class="modern-form-label">
                                            {{ $type == 2 ? 'سعر الخدمة' : __('basicdata::models/db_products.fields.prod_price') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light {{ $type == 2 ? 'text-success' : 'text-primary' }} fw-bold border" style="border-radius: 0 0.625rem 0.625rem 0;">{{ config('app.currency', 'SAR') }}</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   wire:model="prod_price" 
                                                   class="form-control modern-input @error('prod_price') is-invalid @enderror" 
                                                   style="border-radius: 0.625rem 0 0 0.625rem;"
                                                   placeholder="0.00" />
                                        </div>
                                        @error('prod_price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Cost Price -->
                                    <div class="col-md-6">
                                        <label class="modern-form-label">
                                            {{ $type == 2 ? 'تكلفة الخدمة' : __('basicdata::models/db_products.fields.cost_price') }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted border" style="border-radius: 0 0.625rem 0.625rem 0;">{{ config('app.currency', 'SAR') }}</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   wire:model="cost_price" 
                                                   class="form-control modern-input @error('cost_price') is-invalid @enderror" 
                                                   style="border-radius: 0.625rem 0 0 0.625rem;"
                                                   placeholder="0.00" />
                                        </div>
                                        @error('cost_price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Tax / VAT -->
                                    <div class="col-md-6">
                                        <label class="modern-form-label">
                                            {{ __('basicdata::models/db_products.fields.vat') }}
                                        </label>
                                        <select wire:model="tax_id" class="form-select modern-input">
                                            <option value="">-- @lang('basicdata::lang.select') --</option>
                                            @foreach($vats as $vId => $vName)
                                                <option value="{{ $vId }}">{{ $vName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <label class="modern-form-label mb-2">
                                            {{ __('basicdata::models/db_products.fields.status') }}
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            @foreach ($statuses as $value => $label)
                                                <label class="d-flex align-items-center gap-2 cursor-pointer p-2 px-3 rounded-2 border {{ (int)$status === (int)$value ? 'bg-light-primary border-primary text-primary fw-bold' : 'bg-light border-light text-muted' }}" style="transition: all 0.2s;">
                                                    <input class="form-check-input mt-0" type="radio" wire:model="status" id="status_{{ $value }}" value="{{ $value }}">
                                                    <span class="fs-7">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="col-md-12">
                                        <label class="modern-form-label">
                                            {{ $type == 2 ? 'أيقونة / صورة الخدمة' : __('basicdata::models/db_products.fields.img') }}
                                        </label>
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="file" wire:model="img" class="form-control modern-input" accept="image/*" />
                                            
                                            @if ($img)
                                                <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0 shadow-xs">
                                                    <img src="{{ $img->temporaryUrl() }}" alt="Preview" class="object-fit-cover w-45px h-45px">
                                                </div>
                                            @elseif ($existing_img)
                                                <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0 shadow-xs">
                                                    <img src="{{ $existing_img }}" alt="Image" class="object-fit-cover w-45px h-45px">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($type != 2)
                                <!-- TAB 2: MULTIPLE UNITS (Products Only) -->
                                <div x-show="activeTab === 'units'" x-transition.opacity.duration.150ms>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold text-gray-800 fs-7 mb-0">
                                            <i class="fa-solid fa-boxes-packing text-success me-2"></i>جدول وحدات القياس والتحويل
                                        </h6>
                                        <button type="button" wire:click="addUnit" class="btn btn-sm btn-light-success d-inline-flex align-items-center gap-1 fs-8 py-1 px-3 rounded-2">
                                            <i class="fa-solid fa-plus fs-9"></i> إضافة وحدة
                                        </button>
                                    </div>

                                    <div class="table-responsive border rounded-3 mb-0">
                                        <table class="table table-row-dashed table-row-gray-200 align-middle gs-4 gy-3 mb-0" style="font-size: 13px;">
                                            <thead class="bg-light text-muted fw-bold fs-8 text-uppercase">
                                                <tr>
                                                    <th style="min-width: 180px;">الوحدة</th>
                                                    <th style="width: 140px;">معامل التحويل</th>
                                                    <th style="width: 140px;" class="text-center">الوحدة الأساسية</th>
                                                    <th style="width: 60px;" class="text-end">حذف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($units as $index => $unitItem)
                                                    <tr>
                                                        <td>
                                                            <select wire:model="units.{{ $index }}.unit_id" class="form-select form-select-sm modern-input">
                                                                <option value="">-- اختر الوحدة --</option>
                                                                @foreach($unitsList as $uId => $uName)
                                                                    <option value="{{ $uId }}">{{ $uName }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   step="0.001" 
                                                                   min="0.001" 
                                                                   wire:model="units.{{ $index }}.conversion_factor" 
                                                                   class="form-control form-control-sm modern-input"
                                                                   placeholder="1.000" />
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="form-check form-check-custom form-check-solid d-inline-block">
                                                                <input class="form-check-input" 
                                                                       type="radio" 
                                                                       name="base_unit_selection" 
                                                                       wire:click="setBaseUnit({{ $index }})" 
                                                                       {{ !empty($unitItem['is_base']) ? 'checked' : '' }} />
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            @if(count($units) > 1)
                                                                <button type="button" wire:click="removeUnit({{ $index }})" class="btn btn-icon btn-sm btn-light-danger rounded-circle">
                                                                    <i class="fa-solid fa-trash-can fs-8"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 3: SIZES (Products Only) -->
                                <div x-show="activeTab === 'sizes'" x-transition.opacity.duration.150ms>
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4 bg-light border">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" wire:model.live="have_sizes" id="have_sizes_switch" />
                                            </div>
                                            <label class="form-check-label fw-bold text-gray-800 fs-7 cursor-pointer" for="have_sizes_switch">
                                                تفعيل المقاسات والأحجام لهذا المنتج
                                            </label>
                                        </div>

                                        @if($have_sizes)
                                            <button type="button" wire:click="addSize" class="btn btn-sm btn-light-danger d-inline-flex align-items-center gap-1 fs-8 py-1 px-3 rounded-2">
                                                <i class="fa-solid fa-plus fs-9"></i> إضافة مقاس
                                            </button>
                                        @endif
                                    </div>

                                    @if($have_sizes)
                                        <div class="table-responsive border rounded-3">
                                            <table class="table table-row-dashed table-row-gray-200 align-middle gs-4 gy-3 mb-0" style="font-size: 13px;">
                                                <thead class="bg-light text-muted fw-bold fs-8 text-uppercase">
                                                    <tr>
                                                        <th style="min-width: 140px;">اسم المقاس (عربي)</th>
                                                        <th style="min-width: 140px;">اسم المقاس (EN)</th>
                                                        <th style="width: 110px;">سعر التكلفة</th>
                                                        <th style="width: 110px;">سعر البيع</th>
                                                        <th style="width: 130px;">الباركود</th>
                                                        <th style="width: 50px;" class="text-end">حذف</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sizes as $sIndex => $sizeItem)
                                                        <tr>
                                                            <td>
                                                                <input type="text" wire:model="sizes.{{ $sIndex }}.ar.name" class="form-control form-control-sm modern-input" placeholder="مثال: كبير" />
                                                            </td>
                                                            <td>
                                                                <input type="text" wire:model="sizes.{{ $sIndex }}.en.name" class="form-control form-control-sm modern-input" placeholder="e.g. Large" />
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" wire:model="sizes.{{ $sIndex }}.cost_price" class="form-control form-control-sm modern-input" placeholder="0.00" />
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" wire:model="sizes.{{ $sIndex }}.sale_price" class="form-control form-control-sm modern-input" placeholder="0.00" />
                                                            </td>
                                                            <td>
                                                                <input type="text" wire:model="sizes.{{ $sIndex }}.barcode" class="form-control form-control-sm modern-input" placeholder="اختياري" />
                                                            </td>
                                                            <td class="text-end">
                                                                <button type="button" wire:click="removeSize({{ $sIndex }})" class="btn btn-icon btn-sm btn-light-danger rounded-circle">
                                                                    <i class="fa-solid fa-trash-can fs-8"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- TAB: DETAILS / DESCRIPTION -->
                            <div x-show="activeTab === 'details'" x-transition.opacity.duration.150ms>
                                <div class="row g-4">
                                    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                        <div class="col-md-6">
                                            <label class="modern-form-label">
                                                {{ $language }} - {{ __('basicdata::models/db_products.fields.details') }}
                                            </label>
                                            <textarea wire:model="details.{{ $locale }}" 
                                                      rows="5" 
                                                      class="form-control modern-input" 
                                                      placeholder="أدخل وصفاً تفصيلياً ({{ $language }})..."></textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer py-3 px-6 border-top d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                            <button type="button" class="btn btn-sm btn-light fs-7 px-4 rounded-2" wire:click="closeModal">
                                @lang('crud.cancel')
                            </button>
                            <button type="submit" class="btn btn-sm btn-save-gradient fs-7 px-5 rounded-2">
                                <i class="fa-solid fa-check fs-8 me-1"></i>
                                @lang('crud.save')
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
