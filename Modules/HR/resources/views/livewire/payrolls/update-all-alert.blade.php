<div>
    @if ($hr_setting->payroll_updated)
    <!--begin::Alert-->
    <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
        <!--begin::Icon-->
        <i class="ki-duotone ki-notification-bing fs-2hx text-primary me-4 mb-5 mb-sm-0"><span
                class="path1"></span><span class="path2"></span><span class="path3"></span></i>
        <!--end::Icon-->

        <!--begin::Wrapper-->
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <!--begin::Title-->
            <h4 class="fw-semibold">تحديث كشف الرواتب</h4>
            <!--end::Title-->

            <!--begin::Content-->
            <span>
               تحتاج لتحديث كشف الرواتب ؟
                <span wire:click="syncPayroll" class="text-primary cursor-pointer">
                   إضغط هنا
                    <div class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="syncPayroll">
                        <span class="visually-hidden">تحميل...</span>
                    </div>
                </span>
            </span>
            <!--end::Content-->
        </div>
        <!--end::Wrapper-->

        <!--begin::Close-->
        <button type="button"
            class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
            data-bs-dismiss="alert">
            <i class="ki-duotone ki-cross fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
        </button>
        <!--end::Close-->
    </div>
    <!--end::Alert-->
    @endif
</div>
