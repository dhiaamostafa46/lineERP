<div class="modal fade" id="customerModal" tabindex="-1" :class="{'show': showCustomerModal}" :style="showCustomerModal ? 'display: block;' : 'display: none;'" aria-hidden="true" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('pos::pos.quick_add_customer') ?? 'إضافة عميل سريع' }}</h5>
                <button type="button" class="btn-close" @click="showCustomerModal = false"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="submitCustomer">
                    <div class="mb-3">
                        <label class="form-label text-end w-100">{{ __('pos::pos.customer_name') ?? 'اسم العميل' }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="newCustomer.name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-end w-100">{{ __('pos::pos.customer_phone') ?? 'رقم الجوال' }}</label>
                        <input type="text" class="form-control" x-model="newCustomer.phone">
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" @click="showCustomerModal = false">{{ __('pos::pos.cancel') ?? 'إلغاء' }}</button>
                        <button type="submit" class="btn btn-primary" :disabled="loadingCustomer">
                            <span x-show="loadingCustomer" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{ __('pos::pos.save') ?? 'حفظ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
