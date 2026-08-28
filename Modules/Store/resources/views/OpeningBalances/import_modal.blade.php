<div class="modal fade" id="importOpeningBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('استيراد رصيد افتتاحي') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form action="{{ route('store.openingbalance.importSave') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">{{ __('اختر ملف الإكسيل') }}</span>
                        </label>
                        <input type="file" name="file" class="form-control form-control-solid" required accept=".xlsx, .xls, .csv">
                        <div class="text-muted fs-7 mt-2">
                            {{ __('صيغ الملفات المدعومة: .xlsx, .xls, .csv') }}
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="fs-6 fw-semibold mb-2">{{ __('تحميل القالب') }}</label>
                        <div class="d-flex align-items-center p-3 border border-dashed border-gray-300 rounded">
                            <i class="fas fa-file-excel fs-1 text-success me-3"></i>
                            <div class="flex-grow-1">
                                <span class="fw-bold text-gray-800 d-block">{{ __('قالب استيراد الرصيد الافتتاحي') }}</span>
                                <span class="text-muted fs-7">{{ __('استخدم هذا القالب لتنظيم بياناتك قبل الرفع') }}</span>
                            </div>
                            <a href="{{ route('store.openingbalance.import') }}?template=1" class="btn btn-sm btn-light-primary">
                                <i class="fas fa-download"></i> {{ __('تحميل') }}
                            </a>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">{{ __('بدء الاستيراد') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
