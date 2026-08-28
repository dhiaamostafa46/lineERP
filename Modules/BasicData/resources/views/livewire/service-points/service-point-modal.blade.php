<div>
    <!-- Service Point Modal -->
    <div wire:ignore.self class="modal fade" id="servicePointModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                
                <!-- Modal Header -->
                <div class="modal-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-gray-900 fs-5">
                        {{ $is_edit ? __('crud.edit') . ' ' . __('basicdata::models/db_service_points.singular') : __('crud.add_new') . ' ' . __('basicdata::models/db_service_points.singular') }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark fs-5"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="save">
                    <div class="modal-body py-4 px-5">
                        
                        <!-- Names (Multilingual) -->
                        <div class="mb-4">
                            @foreach (config('langs', ['ar' => 'العربية', 'en' => 'English']) as $locale => $language)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7 required">
                                        {{ $language }} - @lang('basicdata::models/db_service_points.fields.name')
                                    </label>
                                    <input type="text" 
                                           wire:model="name.{{ $locale }}" 
                                           class="form-control form-control-solid fs-7 @error('name.'.$locale) is-invalid @enderror" 
                                           placeholder="أدخل اسم نقطة الخدمة بـ {{ $language }}" />
                                    @error('name.'.$locale)
                                        <div class="invalid-feedback fs-8">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- Code -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7">
                                @lang('basicdata::models/db_service_points.fields.code')
                            </label>
                            <input type="text" wire:model="code" class="form-control form-control-solid fs-7" placeholder="الكود..." />
                            @error('code') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type & Status -->
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold fs-7 required">
                                    @lang('basicdata::models/db_service_points.fields.type')
                                </label>
                                <select wire:model="type" class="form-select form-select-solid fs-7">
                                    <option value="1">نقاط البيع (POS)</option>
                                    <option value="2">مطبخ (Kitchen)</option>
                                    <option value="3">خدمة أخرى (Other)</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label fw-semibold fs-7 required">
                                    @lang('basicdata::models/db_service_points.fields.status')
                                </label>
                                <select wire:model="status" class="form-select form-select-solid fs-7">
                                    <option value="1">@lang('basicdata::lang.active')</option>
                                    <option value="0">@lang('basicdata::lang.inactive')</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-top py-3 px-5 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light fs-7" data-bs-dismiss="modal">
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

    <!-- Livewire Modal JS Hooks -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modalEl = document.getElementById('servicePointModal');
            let bsModal = null;
            if (modalEl && typeof bootstrap !== 'undefined') {
                bsModal = new bootstrap.Modal(modalEl);
            }

            @this.on('open-service-point-modal', () => {
                if (modalEl && typeof bootstrap !== 'undefined') {
                    if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
                    bsModal.show();
                }
            });

            @this.on('close-service-point-modal', () => {
                if (bsModal) bsModal.hide();
            });

            @this.on('service-point-saved', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: event.message || 'تم حفظ نقطة الخدمة بنجاح.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            });
        });
    </script>
</div>
