<div>
    <!-- Kitchen Modal -->
    <div wire:ignore.self class="modal fade" id="kitchenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                
                <!-- Modal Header -->
                <div class="modal-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-gray-900 fs-5">
                        {{ $is_edit ? __('crud.edit') . ' ' . __('basicdata::models/db_kitchens.singular') : __('crud.add_new') . ' ' . __('basicdata::models/db_kitchens.singular') }}
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
                                        {{ $language }} - @lang('basicdata::models/db_kitchens.fields.name')
                                    </label>
                                    <input type="text" 
                                           wire:model="name.{{ $locale }}" 
                                           class="form-control form-control-solid fs-7 @error('name.'.$locale) is-invalid @enderror" 
                                           placeholder="أدخل اسم المطبخ بـ {{ $language }}" />
                                    @error('name.'.$locale)
                                        <div class="invalid-feedback fs-8">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- Barcode -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7">
                                @lang('basicdata::models/db_kitchens.fields.barcode')
                            </label>
                            <input type="text" wire:model="barcode" class="form-control form-control-solid fs-7" placeholder="الباركود..." />
                            @error('barcode') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-7 required">
                                @lang('basicdata::models/db_kitchens.fields.status')
                            </label>
                            <select wire:model="status" class="form-select form-select-solid fs-7">
                                <option value="1">@lang('basicdata::lang.active')</option>
                                <option value="0">@lang('basicdata::lang.inactive')</option>
                            </select>
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
            const modalEl = document.getElementById('kitchenModal');
            let bsModal = null;
            if (modalEl && typeof bootstrap !== 'undefined') {
                bsModal = new bootstrap.Modal(modalEl);
            }

            @this.on('open-kitchen-modal', () => {
                if (modalEl && typeof bootstrap !== 'undefined') {
                    if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
                    bsModal.show();
                }
            });

            @this.on('close-kitchen-modal', () => {
                if (bsModal) bsModal.hide();
            });

            @this.on('kitchen-saved', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح!',
                    text: event.message || 'تم حفظ المطبخ بنجاح.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            });
        });
    </script>
</div>
