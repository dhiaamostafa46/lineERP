<div>
    @if($isOpen)
        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show" style="z-index: 1050;" wire:click="closeModal"></div>

        <!-- Product/Service Modal Dialog -->
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-xl rounded-4" style="background: #ffffff;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
                        <h5 class="modal-title fw-bold text-gray-900 fs-5">
                            @if($is_edit)
                                {{ __('crud.edit') }} {{ $type == 2 ? 'خدمة (Service)' : __('basicdata::models/db_products.singular') }}
                            @else
                                {{ __('crud.add_new') }} {{ $type == 2 ? 'خدمة (Service)' : __('basicdata::models/db_products.singular') }}
                            @endif
                        </h5>
                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" wire:click="closeModal" aria-label="Close">
                            <i class="fa-solid fa-xmark fs-5"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body py-4 px-5">
                            
                            <!-- Type & Status Selection -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7 required">
                                        @lang('basicdata::models/db_products.fields.type')
                                    </label>
                                    <select wire:model.live="type" class="form-select form-select-solid fs-7">
                                        <option value="1">📦 منتج مخزني (Product)</option>
                                        <option value="2">🛎️ خدمة (Service)</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7 required">
                                        @lang('basicdata::models/db_products.fields.status')
                                    </label>
                                    <select wire:model="status" class="form-select form-select-solid fs-7">
                                        <option value="1">@lang('basicdata::lang.active')</option>
                                        <option value="0">@lang('basicdata::lang.inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Names (Multilingual) -->
                            <div class="row g-3 mb-4">
                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold fs-7 required">
                                            {{ $language }} - @lang('basicdata::models/db_products.fields.name')
                                        </label>
                                        <input type="text" 
                                               wire:model="name.{{ $locale }}" 
                                               class="form-control form-control-solid fs-7 @error('name.'.$locale) is-invalid @enderror" 
                                               placeholder="أدخل الاسم بـ {{ $language }}" />
                                        @error('name.'.$locale)
                                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <!-- Barcode & Category -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7">
                                        @lang('basicdata::models/db_products.fields.barcode')
                                    </label>
                                    <input type="text" 
                                           wire:model="barcode" 
                                           class="form-control form-control-solid fs-7 font-monospace" 
                                           placeholder="الباركود (Barcode)..." />
                                    @error('barcode') <div class="text-danger fs-8">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7 required">
                                        @lang('basicdata::models/db_products.fields.category_id')
                                    </label>
                                    <select wire:model="category_id" class="form-select form-select-solid fs-7 @error('category_id') is-invalid @enderror">
                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Pricing: Sale Price & Cost Price -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7 required">
                                        @lang('basicdata::models/db_products.fields.prod_price') (سعر البيع)
                                    </label>
                                    <div class="input-group input-group-solid">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               wire:model="prod_price" 
                                               class="form-control form-control-solid fs-7 font-monospace @error('prod_price') is-invalid @enderror" 
                                               placeholder="0.00" />
                                        <span class="input-group-text fs-8 text-muted">{{ config('app.currency', 'SAR') }}</span>
                                    </div>
                                    @error('prod_price') <div class="text-danger fs-8">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7">
                                        @lang('basicdata::models/db_products.fields.cost_price') (سعر التكلفة)
                                    </label>
                                    <div class="input-group input-group-solid">
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               wire:model="cost_price" 
                                               class="form-control form-control-solid fs-7 font-monospace" 
                                               placeholder="0.00" />
                                        <span class="input-group-text fs-8 text-muted">{{ config('app.currency', 'SAR') }}</span>
                                    </div>
                                    @error('cost_price') <div class="text-danger fs-8">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Unit & Tax Account -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7">
                                        @lang('basicdata::models/db_products.fields.base_unit_id')
                                    </label>
                                    <select wire:model="base_unit_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7">
                                        @lang('basicdata::models/db_products.fields.tax_id') (الضريبة)
                                    </label>
                                    <select wire:model="tax_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- بدون ضريبة --</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold fs-7">
                                    @lang('basicdata::models/db_products.fields.img')
                                </label>
                                <input type="file" wire:model="img" class="form-control form-control-solid fs-7" accept="image/*" />
                                @error('img') <span class="text-danger fs-8">{{ $message }}</span> @enderror

                                <!-- Preview -->
                                <div class="mt-2">
                                    @if ($img)
                                        <img src="{{ $img->temporaryUrl() }}" class="rounded-circle border object-fit-cover" style="width: 48px; height: 48px;">
                                    @elseif ($existing_img)
                                        <img src="{{ $existing_img }}" class="rounded-circle border object-fit-cover" style="width: 48px; height: 48px;">
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-top py-3 px-5 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-light fs-7" wire:click="closeModal">
                                @lang('basicdata::lang.cancel')
                            </button>
                            <button type="submit" class="btn btn-sm front-btn-primary fs-7" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fa-solid fa-check fs-8"></i>
                                    @lang('basicdata::lang.save')
                                </span>
                                <span wire:loading>
                                    <i class="fa-solid fa-spinner fa-spin fs-8"></i>
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
