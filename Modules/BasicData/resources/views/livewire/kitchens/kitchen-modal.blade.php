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

        <!-- Kitchen Modal Dialog -->
        <div class="modal fade show d-block premium-modal-dialog" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content premium-modal-content overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                <i class="fa-solid fa-utensils fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    {{ $is_edit ? __('crud.edit') : __('crud.add_new') }} {{ __('basicdata::models/db_kitchens.singular') }}
                                </h4>
                                <span class="text-muted fs-8">إدارة أقسام التحضير والطباعة</span>
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
                                            {{ $language }} - @lang('basicdata::models/db_kitchens.fields.name') <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               wire:model="name.{{ $locale }}" 
                                               class="form-control modern-input @error('name.'.$locale) is-invalid @enderror" 
                                               placeholder="أدخل اسم المطبخ بـ {{ $language }}" />
                                        @error('name.'.$locale)
                                            <div class="invalid-feedback fs-8">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <!-- Barcode -->
                            <div class="mb-4">
                                <label class="modern-form-label">
                                    @lang('basicdata::models/db_kitchens.fields.barcode')
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border" style="border-radius: 0 0.625rem 0.625rem 0;"><i class="fa-solid fa-barcode"></i></span>
                                    <input type="text" 
                                           wire:model="barcode" 
                                           class="form-control modern-input" 
                                           style="border-radius: 0.625rem 0 0 0.625rem;"
                                           placeholder="كود المطبخ / الباركود" />
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-2">
                                <label class="modern-form-label mb-2">
                                    @lang('basicdata::models/db_kitchens.fields.status') <span class="text-danger">*</span>
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
