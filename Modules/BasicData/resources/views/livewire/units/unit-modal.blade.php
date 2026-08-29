<div>
    @if($isOpen)
        @include('basicdata::layouts.partials._modal_styles')

        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show premium-modal-backdrop" wire:click="closeModal"></div>

        <!-- Unit Modal Dialog -->
        <div class="modal fade show d-block premium-modal-dialog" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content premium-modal-content overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                <i class="fa-solid fa-scale-balanced fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ __('basicdata::models/db_units.singular') }}
                                </h4>
                                <span class="text-muted fs-8">إدارة وتعديل وحدات القياس</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none hover-scale" wire:click="closeModal" aria-label="Close" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-6" style="background-color: #ffffff;">
                            
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

                            <!-- Names (Multilingual) -->
                            <div class="row g-4 mb-4">
                                @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                    <div class="col-12">
                                        <label class="modern-form-label">
                                            {{ $language }} - @lang('basicdata::models/db_units.fields.name') <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               wire:model="name.{{ $locale }}" 
                                               class="form-control modern-input @error('name.'.$locale) is-invalid @enderror" 
                                               placeholder="أدخل اسم الوحدة بـ {{ $language }}" />
                                        @error('name.'.$locale)
                                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <!-- Status -->
                            <div class="mb-2">
                                <label class="modern-form-label mb-2">
                                    @lang('basicdata::models/db_units.fields.status') <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <label class="d-flex align-items-center gap-2 cursor-pointer p-2 px-3 rounded-2 border {{ (int)$status === 1 ? 'bg-light-primary border-primary text-primary fw-bold' : 'bg-light border-light text-muted' }}" style="transition: all 0.2s;">
                                        <input class="form-check-input mt-0" type="radio" wire:model="status" value="1">
                                        <span class="fs-7">@lang('basicdata::lang.active')</span>
                                    </label>
                                    <label class="d-flex align-items-center gap-2 cursor-pointer p-2 px-3 rounded-2 border {{ (int)$status === 0 ? 'bg-light-primary border-primary text-primary fw-bold' : 'bg-light border-light text-muted' }}" style="transition: all 0.2s;">
                                        <input class="form-check-input mt-0" type="radio" wire:model="status" value="0">
                                        <span class="fs-7">@lang('basicdata::lang.inactive')</span>
                                    </label>
                                </div>
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
