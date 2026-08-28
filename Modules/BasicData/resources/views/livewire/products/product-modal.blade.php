<div>
    @if($isOpen)
        <style>
            .premium-modal-backdrop {
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                z-index: 1050;
            }
            .premium-modal-dialog {
                z-index: 1055;
                animation: modalScaleUp 0.24s cubic-bezier(0.16, 1, 0.3, 1);
            }
            @keyframes modalScaleUp {
                0% { opacity: 0; transform: scale(0.96) translateY(10px); }
                100% { opacity: 1; transform: scale(1) translateY(0); }
            }
            .premium-modal-content {
                border: 1px solid rgba(226, 232, 240, 0.8);
                border-radius: 1.25rem;
                box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3);
                background: #ffffff;
            }
            /* Segmented Tabs Control */
            .segmented-tabs-wrapper {
                background: #f1f5f9;
                padding: 0.35rem;
                border-radius: 0.75rem;
                display: inline-flex;
                gap: 0.25rem;
            }
            .segmented-tab-btn {
                border: none;
                background: transparent;
                padding: 0.5rem 1.25rem;
                font-size: 0.85rem;
                font-weight: 600;
                color: #64748b;
                border-radius: 0.5rem;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
            }
            .segmented-tab-btn:hover {
                color: #1e293b;
            }
            .segmented-tab-btn.active {
                background: #ffffff;
                color: #0f172a;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }
            /* Modern Inputs */
            .modern-form-label {
                font-size: 0.8125rem;
                font-weight: 600;
                color: #334155;
                margin-bottom: 0.35rem;
                display: block;
            }
            .modern-input {
                border: 1px solid #cbd5e1;
                border-radius: 0.625rem;
                padding: 0.6rem 0.85rem;
                font-size: 0.875rem;
                background: #ffffff;
                color: #0f172a;
                transition: all 0.18s ease;
            }
            .modern-input:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                outline: none;
                background: #ffffff;
            }
            .modern-unit-card {
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                border-radius: 0.75rem;
                padding: 1rem;
                transition: all 0.2s ease;
            }
            .modern-unit-card:hover {
                border-color: #cbd5e1;
                background: #ffffff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            }
            .btn-save-gradient {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                color: #ffffff;
                font-weight: 600;
                border-radius: 0.625rem;
                padding: 0.55rem 1.75rem;
                border: none;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
                transition: all 0.2s ease;
            }
            .btn-save-gradient:hover {
                background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
                color: #ffffff;
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
            }
        </style>

        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show premium-modal-backdrop" wire:click="closeModal"></div>

        <!-- Product Modal Dialog -->
        <div class="modal fade show d-block premium-modal-dialog" tabindex="-1" aria-modal="true" role="dialog" x-data="{ activeTab: 'basic' }">
            <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 920px;">
                <div class="modal-content premium-modal-content overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 44px; height: 44px; background: {{ $type == 2 ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)' }}; color: #ffffff;">
                                <i class="{{ $type == 2 ? 'fa-solid fa-bell-concierge' : 'fa-solid fa-box-open' }} fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ $type == 2 ? __('basicdata::models/db_products.service') : __('basicdata::models/db_products.product') }}
                                </h4>
                                <span class="text-muted fs-8">
                                    {{ $type == 2 ? __('basicdata::models/db_products.fields.details') : __('basicdata::models/db_products.singular') }}
                                </span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none hover-scale" wire:click="closeModal" aria-label="Close" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Navigation Segmented Tabs (تصميم كبسولات حديث) -->
                    <div class="px-6 py-3 border-bottom" style="background: #f8fafc;">
                        <div class="segmented-tabs-wrapper w-100 justify-content-start">
                            <!-- Tab 1: Basic Info -->
                            <button type="button" 
                                    class="segmented-tab-btn flex-fill justify-content-center" 
                                    :class="activeTab === 'basic' ? 'active text-primary' : ''" 
                                    @click="activeTab = 'basic'">
                                <i class="fa-solid fa-circle-info fs-7"></i>
                                <span>{{ __('basicdata::models/db_products.sections.basic_info') }}</span>
                            </button>

                            <!-- Tab 2: Multiple Units -->
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

                            <!-- Tab 3: Sizes & Variations -->
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
                                                {{ $language }} - {{ __('basicdata::models/db_products.fields.name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="name.{{ $locale }}" 
                                                   class="form-control modern-input @error('name.'.$locale) is-invalid @enderror" 
                                                   placeholder="{{ __('basicdata::models/db_products.placeholders.name') }} ({{ $language }})" />
                                            @error('name.'.$locale)
                                                <div class="invalid-feedback fs-8">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach

                                    <!-- Barcode -->
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
                                            {{ __('basicdata::models/db_products.fields.prod_price') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-primary fw-bold border" style="border-radius: 0 0.625rem 0.625rem 0;">{{ config('app.currency', 'SAR') }}</span>
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
                                            {{ __('basicdata::models/db_products.fields.cost_price') }}
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
                                            {{ __('basicdata::models/db_products.fields.img') }}
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
                                        @error('img') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Multilingual Details -->
                                    @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                        <div class="col-md-6">
                                            <label class="modern-form-label">
                                                {{ $language }} - {{ __('basicdata::models/db_products.fields.details') }}
                                            </label>
                                            <textarea wire:model="details.{{ $locale }}" 
                                                      class="form-control modern-input" 
                                                      rows="2" 
                                                      placeholder="{{ __('basicdata::models/db_products.placeholders.details') }} ({{ $language }})"></textarea>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- TAB 2: MULTIPLE UNITS -->
                            <div x-show="activeTab === 'units'" x-transition.opacity.duration.150ms style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h6 class="fw-bold text-gray-900 mb-1 fs-6">{{ __('basicdata::models/db_products.sections.units') }}</h6>
                                        <span class="text-muted fs-8">{{ __('basicdata::models/db_products.unit.conversion_factor') }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light-primary fw-bold rounded-2 px-3" wire:click="addUnitRow">
                                        <i class="fas fa-plus fs-8 me-1"></i> {{ __('basicdata::models/db_products.sections.add_unit') }}
                                    </button>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    @foreach ($units as $index => $unit)
                                        <div class="modern-unit-card d-flex flex-wrap align-items-center justify-content-between gap-3" wire:key="unit-row-{{ $index }}">
                                            <div class="row g-3 flex-grow-1 align-items-center">
                                                <div class="col-md-5">
                                                    <label class="modern-form-label fs-8 mb-1">{{ __('basicdata::models/db_products.unit.unit_id') }}:</label>
                                                    <select wire:model="units.{{ $index }}.unit_id" class="form-select modern-input form-select-sm fs-7">
                                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                                        @foreach($unitsList as $uId => $uName)
                                                            <option value="{{ $uId }}">{{ $uName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="modern-form-label fs-8 mb-1">{{ __('basicdata::models/db_products.unit.conversion_factor') }}:</label>
                                                    <input type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           wire:model="units.{{ $index }}.conversion_factor" 
                                                           class="form-control modern-input form-control-sm fs-7" 
                                                           placeholder="1.00" />
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="modern-form-label fs-8 mb-1">{{ __('basicdata::models/db_products.unit.is_base') }}:</label>
                                                    <div class="form-check form-switch mt-1">
                                                        <input class="form-check-input cursor-pointer" type="checkbox" wire:model="units.{{ $index }}.is_base" id="is_base_{{ $index }}" style="width: 2.2rem; height: 1.2rem;">
                                                        <label class="form-check-label fs-8 text-gray-700 fw-semibold cursor-pointer ms-2" for="is_base_{{ $index }}">
                                                            {{ __('basicdata::models/db_products.unit.is_base') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                @if(count($units) > 1)
                                                    <button type="button" class="btn btn-icon btn-sm btn-light-danger rounded-circle" wire:click="removeUnitRow({{ $index }})" title="@lang('crud.delete')">
                                                        <i class="fas fa-trash-can fs-8"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- TAB 3: SIZES & VARIATIONS -->
                            <div x-show="activeTab === 'sizes'" x-transition.opacity.duration.150ms style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                    <div class="form-check form-switch d-flex align-items-center gap-2">
                                        <input class="form-check-input cursor-pointer" type="checkbox" wire:model.live="have_sizes" id="have_sizes_switch" style="width: 2.5rem; height: 1.35rem;">
                                        <label class="form-check-label fw-bold fs-7 text-gray-900 cursor-pointer" for="have_sizes_switch">
                                            {{ __('basicdata::models/db_products.fields.have_sizes') }}
                                        </label>
                                    </div>
                                    @if($have_sizes)
                                        <button type="button" class="btn btn-sm btn-light-primary fw-bold rounded-2 px-3" wire:click="addSizeRow">
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
                                                                       class="form-control modern-input form-control-sm fs-8" 
                                                                       placeholder="{{ __('basicdata::models/db_products.placeholders.size_name') }}" />
                                                            </td>
                                                        @endforeach
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text bg-light text-muted border fs-8">{{ config('app.currency', 'SAR') }}</span>
                                                                <input type="number" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       wire:model="sizes.{{ $index }}.cost_price" 
                                                                       class="form-control modern-input form-control-sm fs-8" 
                                                                       placeholder="0.00" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text bg-light text-primary fw-bold border fs-8">{{ config('app.currency', 'SAR') }}</span>
                                                                <input type="number" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       wire:model="sizes.{{ $index }}.sale_price" 
                                                                       class="form-control modern-input form-control-sm fs-8" 
                                                                       placeholder="0.00" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" 
                                                                   wire:model="sizes.{{ $index }}.barcode" 
                                                                   class="form-control modern-input form-control-sm fs-8" 
                                                                   placeholder="{{ __('basicdata::models/db_products.placeholders.barcode') }}" />
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-icon btn-sm btn-light-danger rounded-circle" wire:click="removeSizeRow({{ $index }})" title="@lang('crud.delete')">
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
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-2" style="width: 50px; height: 50px;">
                                            <i class="fa-solid fa-ruler-combined fs-4 text-gray-400"></i>
                                        </div>
                                        <p class="fs-7 text-gray-600 mb-0">{{ __('basicdata::models/db_products.fields.have_sizes') }}</p>
                                    </div>
                                @endif
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer py-4 px-6 border-top d-flex justify-content-between align-items-center bg-light-subtle">
                            <button type="button" class="btn btn-sm btn-light text-gray-700 fw-semibold fs-7 px-4 rounded-2" wire:click="closeModal">
                                <i class="fa-solid fa-xmark fs-8 me-1"></i>
                                @lang('crud.cancel')
                            </button>
                            <button type="submit" class="btn btn-sm btn-save-gradient fs-7 px-5" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fa-solid fa-check fs-8 me-1"></i>
                                    @lang('crud.save')
                                </span>
                                <span wire:loading>
                                    <i class="fa-solid fa-spinner fa-spin fs-8 me-1"></i>
                                    @lang('basicdata::lang.saving')
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif
</div>
