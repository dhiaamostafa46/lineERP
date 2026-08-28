<div>
    @if($isOpen)
        <!-- Modal Backdrop with blur -->
        <div class="modal-backdrop fade show" style="z-index: 1050; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);" wire:click="closeModal"></div>

        <!-- Category Modal Dialog -->
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="background: #ffffff;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                <i class="fa-solid fa-folder-tree fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bolder text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ __('basicdata::models/db_categories.singular') }}
                                </h4>
                                <span class="text-muted fs-8">إدارة الفئات الرئيسية والفرعية وصور الأقسام</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none" wire:click="closeModal" aria-label="Close" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-5" style="background-color: #fafbfc;">
                            
                            <!-- Names (Multilingual) -->
                            <div class="row g-3 mb-4">
                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold fs-7 text-gray-700 mb-1">
                                            {{ $language }} - @lang('basicdata::models/db_categories.fields.name') <span class="text-danger">*</span>
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

                            <!-- Parent Category & Status & Type -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold fs-7 text-gray-700 mb-1">
                                        @lang('basicdata::models/db_categories.fields.parent_id')
                                    </label>
                                    <select wire:model="parent_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                        @foreach($parentCategories as $pId => $pName)
                                            <option value="{{ $pId }}">{{ $pName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold fs-7 text-gray-700 mb-1">
                                        @lang('basicdata::models/db_categories.fields.status') <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="status" class="form-select form-select-solid fs-7">
                                        <option value="1">@lang('basicdata::lang.active')</option>
                                        <option value="0">@lang('basicdata::lang.inactive')</option>
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label fw-semibold fs-7 text-gray-700 mb-1">
                                        @lang('basicdata::models/db_categories.fields.type') <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="type" class="form-select form-select-solid fs-7">
                                        <option value="1">@lang('basicdata::lang.active')</option>
                                        <option value="0">@lang('basicdata::lang.inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold fs-7 text-gray-700 mb-1">
                                    @lang('basicdata::models/db_categories.fields.img')
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="file" wire:model="img" class="form-control form-control-solid fs-7" accept="image/*" />
                                    @if ($img)
                                        <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0">
                                            <img src="{{ $img->temporaryUrl() }}" class="object-fit-cover w-45px h-45px" alt="Preview">
                                        </div>
                                    @elseif ($existing_img)
                                        <div class="symbol symbol-45px border rounded-3 overflow-hidden flex-shrink-0">
                                            <img src="{{ $existing_img }}" class="object-fit-cover w-45px h-45px" alt="Image">
                                        </div>
                                    @endif
                                </div>
                                @error('img') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer py-3 px-6 border-top d-flex justify-content-between align-items-center bg-white">
                            <button type="button" class="btn btn-sm btn-light fs-7 px-4" wire:click="closeModal">
                                <i class="fa-solid fa-xmark fs-8 me-1"></i>
                                @lang('basicdata::lang.cancel')
                            </button>
                            <button type="submit" class="btn btn-sm front-btn-primary fs-7 px-5 shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fa-solid fa-check fs-8 me-1"></i>
                                    @lang('basicdata::lang.save')
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
