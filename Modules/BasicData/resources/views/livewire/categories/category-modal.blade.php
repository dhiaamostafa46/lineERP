<div>
    @if($isOpen)
        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show" style="z-index: 1050;" wire:click="closeModal"></div>

        <!-- Category Modal Dialog -->
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1055;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-xl rounded-4" style="background: #ffffff;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
                        <h5 class="modal-title fw-bold text-gray-900 fs-5">
                            {{ $is_edit ? __('crud.edit') . ' ' . __('basicdata::models/db_categories.singular') : __('crud.add_new') . ' ' . __('basicdata::models/db_categories.singular') }}
                        </h5>
                        <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" wire:click="closeModal" aria-label="Close">
                            <i class="fa-solid fa-xmark fs-5"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body py-4 px-5">
                            
                            <!-- Names (Multilingual) -->
                            <div class="row g-3 mb-4">
                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                    <div class="col-sm-6">
                                        <label class="form-label fw-semibold fs-7 required">
                                            {{ $language }} - @lang('basicdata::models/db_categories.fields.name')
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

                            <!-- Parent Category & Status -->
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7">
                                        @lang('basicdata::models/db_categories.fields.parent_id')
                                    </label>
                                    <select wire:model="parent_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- @lang('basicdata::lang.select') --</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold fs-7 required">
                                        @lang('basicdata::models/db_categories.fields.status')
                                    </label>
                                    <select wire:model="status" class="form-select form-select-solid fs-7">
                                        <option value="1">@lang('basicdata::lang.active')</option>
                                        <option value="0">@lang('basicdata::lang.inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold fs-7">
                                    @lang('basicdata::models/db_categories.fields.img')
                                </label>
                                <input type="file" wire:model="img" class="form-control form-control-solid fs-7" accept="image/*" />
                                @error('img') <span class="text-danger fs-8">{{ $message }}</span> @enderror

                                <!-- Preview -->
                                <div class="mt-2">
                                    @if ($img)
                                        <img src="{{ $img->temporaryUrl() }}" class="rounded-3 border object-fit-cover" style="width: 60px; height: 60px;">
                                    @elseif ($existing_img)
                                        <img src="{{ $existing_img }}" class="rounded-3 border object-fit-cover" style="width: 60px; height: 60px;">
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
