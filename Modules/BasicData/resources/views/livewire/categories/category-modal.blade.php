<div>
    @if($isOpen)
        @include('basicdata::layouts.partials._modal_styles')

        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show premium-modal-backdrop" wire:click="closeModal"></div>

        <!-- Category Modal Dialog -->
        <div class="modal fade show d-block premium-modal-dialog" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content premium-modal-content overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                <i class="fa-solid fa-folder-tree fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ __('basicdata::models/db_categories.singular') }}
                                </h4>
                                <span class="text-muted fs-8">إدارة الفئات والتصنيفات وصور الأقسام</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none hover-scale" wire:click="closeModal" aria-label="Close" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-6" style="background-color: #ffffff;">
                            
                            <!-- Names (Multilingual) -->
                            <div class="row g-4 mb-4">
                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                    <div class="col-sm-6">
                                        <label class="modern-form-label">
                                            {{ $language }} - @lang('basicdata::models/db_categories.fields.name') <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               wire:model="name.{{ $locale }}" 
                                               class="form-control modern-input @error('name.'.$locale) is-invalid @enderror" 
                                               placeholder="أدخل الاسم بـ {{ $language }}" />
                                        @error('name.'.$locale)
                                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <!-- Parent Category & Status & Type -->
                            <div class="row g-4 mb-4">
                                <div class="col-sm-4">
                                    <label class="modern-form-label">
                                        @lang('basicdata::models/db_categories.fields.parent_id')
                                    </label>
                                    <select wire:model="parent_id" class="form-select modern-input">
                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                        @foreach($parentCategories as $pId => $pName)
                                            <option value="{{ $pId }}">{{ $pName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="modern-form-label">
                                        @lang('basicdata::models/db_categories.fields.status') <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="status" class="form-select modern-input">
                                        <option value="1">@lang('basicdata::lang.active')</option>
                                        <option value="0">@lang('basicdata::lang.inactive')</option>
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="modern-form-label">
                                        @lang('basicdata::models/db_categories.fields.type') <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="type" class="form-select modern-input">
                                        <option value="1">@lang('basicdata::lang.active')</option>
                                        <option value="0">@lang('basicdata::lang.inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-2">
                                <label class="modern-form-label">
                                    @lang('basicdata::models/db_categories.fields.img')
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="file" wire:model="img" class="form-control modern-input" accept="image/*" />
                                    @if ($img)
                                        <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0 shadow-xs">
                                            <img src="{{ $img->temporaryUrl() }}" class="object-fit-cover w-45px h-45px" alt="Preview">
                                        </div>
                                    @elseif ($existing_img)
                                        <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0 shadow-xs">
                                            <img src="{{ $existing_img }}" class="object-fit-cover w-45px h-45px" alt="Image">
                                        </div>
                                    @endif
                                </div>
                                @error('img') <span class="text-danger fs-8">{{ $message }}</span> @enderror
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
