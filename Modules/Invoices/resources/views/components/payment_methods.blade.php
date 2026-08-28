@php
    $langPrefix = $langPrefix ?? 'invoices::models/sales_invoices';
@endphp
<!-- Payment Status Summary Banner (Hidden when payment method is added & selected) -->
<div class="mb-3" id="payment_status_card">
    <div class="alert alert-light-warning d-flex align-items-center p-3 mb-0 border border-warning border-dashed rounded-3 shadow-xs" id="payment_status_box">
        <i class="ki-duotone ki-document fs-2x text-warning me-3" id="payment_status_icon"><span class="path1"></span><span class="path2"></span></i>
        <div class="d-flex flex-column">
            <span class="fw-bold fs-7 text-dark" id="payment_status_title">
                {{ Lang::has($langPrefix . '.ui.payment_status_credit_full') ? __($langPrefix . '.ui.payment_status_credit_full') : 'طريقة السداد: آجل بالكامل (على حساب العميل)' }}
            </span>
            <span class="fs-8 text-muted mt-1" id="payment_status_desc">
                {{ Lang::has($langPrefix . '.ui.payment_status_credit_desc') ? __($langPrefix . '.ui.payment_status_credit_desc') : 'لم يتم اختيار وسيلة دفع، سيتم تسجيل إجمالي الفاتورة كـ دَيْن مستحق على الحساب.' }}
            </span>
        </div>
    </div>
</div>

<div id="payments_container">
    <!-- Dynamic Payment Rows -->
</div>

<div class="d-flex align-items-center justify-content-between mt-3">
    <button type="button" class="btn btn-light-primary btn-sm fw-bold fs-7 px-4 py-2 rounded-3 shadow-xs d-inline-flex align-items-center" onclick="addPayment()">
        <span class="badge badge-primary me-2 p-1 d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
            <i class="ki-duotone ki-plus fs-5 text-white"><span class="path1"></span><span class="path2"></span></i>
        </span>
        <span class="text-primary fw-bolder">
            {{ Lang::has($langPrefix . '.ui.add_payment_method_btn') ? __($langPrefix . '.ui.add_payment_method_btn') : (Lang::has($langPrefix . '.ui.add_payment_method') ? __($langPrefix . '.ui.add_payment_method') : 'إضافة وسيلة دفع (نقداً / شبكة / تحويل)') }}
        </span>
    </button>
</div>



