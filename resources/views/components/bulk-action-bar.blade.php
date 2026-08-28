@props(['route' => ''])

<div id="frontBulkActionBar" 
     class="position-fixed bottom-0 start-50 translate-middle-x mb-6 bg-dark text-white py-3 px-5 rounded-pill shadow-xl d-none align-items-center gap-4 transition-all" 
     style="z-index: 1060; min-width: 320px; border: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);">
    
    <div class="d-flex align-items-center gap-2">
        <span class="badge rounded-pill bg-primary fs-8 px-2 py-1" id="bulkSelectedCount">0</span>
        <span class="fs-7 fw-semibold">عناصر محددة</span>
    </div>

    <div class="d-flex align-items-center gap-2 ms-auto">
        <button type="button" 
                class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1 rounded-pill px-3" 
                onclick="executeBulkDelete('{{ $route }}')">
            <i class="fa-solid fa-trash-can fs-8"></i>
            <span>حذف المحدد</span>
        </button>

        <button type="button" 
                class="btn btn-sm btn-icon btn-ghost-secondary text-white-50 text-hover-white rounded-circle" 
                onclick="clearAllSelections()" 
                title="إلغاء التحديد">
            <i class="fa-solid fa-xmark fs-7"></i>
        </button>
    </div>
</div>
