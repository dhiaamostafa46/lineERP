<div>
    @if($isOpen)
        @include('basicdata::layouts.partials._modal_styles')

        <!-- Modal Backdrop -->
        <div class="modal-backdrop fade show premium-modal-backdrop" wire:click="closeModal"></div>

        <!-- Product Import Modal Dialog -->
        <div class="modal fade show d-block premium-modal-dialog" tabindex="-1" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 720px;">
                <div class="modal-content premium-modal-content overflow-hidden">
                    
                    <!-- Modal Header -->
                    <div class="modal-header py-4 px-6 border-bottom d-flex align-items-center justify-content-between" style="background: #ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-xs" 
                                 style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                <i class="fa-solid fa-file-import fs-4"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-gray-900 mb-0 fs-5">
                                    @lang('crud.import') @lang('basicdata::models/db_products.plural')
                                </h4>
                                <span class="text-muted fs-8">
                                    استيراد المنتجات والخدمات جماعياً عبر ملف Excel بكل سهولة
                                </span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle shadow-none hover-scale" wire:click="closeModal" aria-label="Close" style="width: 34px; height: 34px;">
                            <i class="fa-solid fa-xmark fs-5 text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-6" style="max-height: calc(80vh - 140px); overflow-y: auto; background-color: #ffffff;">
                        
                        <!-- Error Alert -->
                        @if ($errorMessage)
                            <div class="alert alert-danger d-flex align-items-center p-3 mb-4 rounded-3 border-0 shadow-sm" style="background-color: #fef2f2; border-right: 4px solid #ef4444 !important;">
                                <i class="fa-solid fa-circle-exclamation fs-4 text-danger me-3"></i>
                                <span class="fs-7 text-danger fw-semibold">{{ $errorMessage }}</span>
                            </div>
                        @endif

                        <!-- Validation Failures -->
                        @if (!empty($failures))
                            <div class="alert alert-danger p-4 mb-4 rounded-3 border-0 shadow-sm" style="background-color: #fef2f2; border-right: 4px solid #ef4444 !important;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                                    <h6 class="mb-0 text-danger fw-bold fs-7">تم العثور على أخطاء في ملف الاستيراد:</h6>
                                </div>
                                <ul class="mb-0 ps-3 fs-8 text-danger" style="max-height: 140px; overflow-y: auto;">
                                    @foreach ($failures as $failure)
                                        <li class="mb-1">
                                            <strong>السطر {{ $failure['row'] }}:</strong>
                                            {{ implode(', ', $failure['errors']) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- File Upload Dropzone -->
                        <div class="mb-4">
                            <label class="modern-form-label mb-2">
                                اختر ملف الإكسل للاستيراد (.xlsx, .xls, .csv) <span class="text-danger">*</span>
                            </label>
                            
                            <div class="position-relative border-2 border-dashed rounded-3 p-5 text-center transition-all" 
                                 style="background: #f8fafc; border-color: #cbd5e1; cursor: pointer;"
                                 onclick="document.getElementById('importFileInput').click()">
                                
                                <input type="file" 
                                       id="importFileInput" 
                                       wire:model="file" 
                                       class="d-none" 
                                       accept=".xlsx, .xls, .csv" />

                                <div wire:loading.remove wire:target="file">
                                    @if ($file)
                                        <i class="fa-solid fa-file-excel text-success fs-1 mb-2 d-block"></i>
                                        <span class="fw-bold text-gray-900 fs-7 d-block">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-muted fs-8">{{ round($file->getSize() / 1024, 2) }} KB</span>
                                    @else
                                        <i class="fa-solid fa-cloud-arrow-up text-primary fs-1 mb-2 d-block"></i>
                                        <span class="fw-bold text-gray-800 fs-7 d-block">انقر هنا لاختيار الملف من جهازك</span>
                                        <span class="text-muted fs-8">يدعم صيغ XLSX, XLS, CSV حتى 20 ميغابايت</span>
                                    @endif
                                </div>

                                <div wire:loading wire:target="file" class="text-primary">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    <span class="fs-7 fw-semibold">جاري رفع ومعالجة الملف...</span>
                                </div>
                            </div>
                            @error('file') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Instructions & Template Download Card -->
                        <div class="rounded-3 p-4 border" style="background: #f1f5f9; border-color: #e2e8f0;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-primary fs-6"></i>
                                    <h6 class="mb-0 fw-bold text-gray-800 fs-7">تعليمات وقالب الاستيراد</h6>
                                </div>
                                <button type="button" 
                                        wire:click="downloadTemplate" 
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm btn-white border shadow-xs d-inline-flex align-items-center gap-2 fs-8 py-1 px-3 rounded-2 text-hover-primary">
                                    <i class="fa-solid fa-file-excel text-success fs-7"></i>
                                    <span>تحميل القالب الجاهز</span>
                                </button>
                            </div>

                            <ul class="text-muted fs-8 mb-0 ps-3" style="line-height: 1.8;">
                                <li>تأكد من مطابقة أسماء الأعمدة للنموذج المرفق وعدم حذف أو تعديل صف العناوين الأول.</li>
                                <li>حقول <strong>(الاسم، الفئة، سعر البيع، الوحدة، الضريبة، والنوع)</strong> حقول إلزامية.</li>
                                <li>حقل النوع يقبل: <code>1</code> للمنتج أو <code>2</code> للخدمة.</li>
                            </ul>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer py-3 px-6 border-top d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                        <button type="button" class="btn btn-sm btn-light fs-7 px-4 rounded-2" wire:click="closeModal">
                            @lang('crud.cancel')
                        </button>
                        
                        <button type="button" 
                                wire:click="import" 
                                wire:loading.attr="disabled"
                                class="btn btn-sm btn-save-gradient fs-7 px-5 rounded-2 d-inline-flex align-items-center gap-2">
                            <span wire:loading.remove wire:target="import">
                                <i class="fa-solid fa-file-import fs-8 me-1"></i>
                                بدء الاستيراد
                            </span>
                            <span wire:loading wire:target="import">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                جاري الاستيراد...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
