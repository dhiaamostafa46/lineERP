<!--begin::Fonts(mandatory for all pages)-->
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Codystar:wght@400;700&family=East+Sea+Dokdo&family=Fredericka+the+Great&family=Gugi&display=swap">

<!--end::Fonts-->
<!--begin::Vendor Stylesheets(used for this page only)-->
<link href="{{ asset('admin_assets') }}/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('admin_assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
    type="text/css" />
<!--end::Vendor Stylesheets-->
<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->


@if (app()->getLocale() == 'ar')
    <link href="{{ asset('admin_assets') }}/css/style.bundle.rtl.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.rtl.css" rel="stylesheet" type="text/css" />
@else
    <link href="{{ asset('admin_assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets') }}/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">



<style>
    /* ==========================================================================
       LineERP Official Brand Design System & SPA Transitions
       #1B325B (LineERP Navy) | #6A669D (LineERP Purple) | #9ABF80 (LineERP Green)
       ========================================================================== */
    :root {
        --bs-primary: #6A669D;
        --bs-primary-active: #1B325B;
        --bs-secondary: #9ABF80;
        --bs-card: #E5E3D4;
        --bs-text-primary: #1E2B50;
        --bs-alert: #F59E0B;
        --bs-link-hover-color: var(--bs-primary-active);
        --bs-body-bg: #F3F5F8;

        --evix-navy: #1B325B;
        --evix-navy-light: #2C436F;
        --evix-purple: #6A669D;
        --evix-purple-light: #8E84BF;
        --evix-green: #9ABF80;
        --evix-green-dark: #2D6A4F;
    }

    /* Livewire SPA Top Navigation Bar */
    .livewire-progress-bar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 3px !important;
        background: linear-gradient(90deg, #6A669D 0%, #9ABF80 50%, #1B325B 100%) !important;
        z-index: 999999 !important;
        box-shadow: 0 0 10px rgba(106, 102, 157, 0.7) !important;
    }

    /* Seamless SPA Content Transition */
    #kt_app_content {
        animation: spaContentFadeIn 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes spaContentFadeIn {
        from {
            opacity: 0.88;
            transform: translateY(3px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ==========================================================================
       LineERP Ultra-Modern Sidebar & Reactive Navigation Design System
       ========================================================================== */
    .line-sidebar-menu {
        padding: 0.5rem 0.75rem;
    }

    .line-section-header {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #8E84BF;
        margin: 1.25rem 0.5rem 0.5rem 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .line-section-header::after {
        content: "";
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(142, 132, 191, 0.25), transparent);
    }

    .line-menu-btn, .line-menu-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.3rem;
        border-radius: 0.65rem;
        background: transparent;
        border: 1px solid transparent;
        color: #3F4254;
        text-decoration: none;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        user-select: none;
    }

    .line-menu-btn:hover, .line-menu-link:hover {
        background: rgba(106, 102, 157, 0.08);
        color: #1B325B;
        transform: translateX(-2px);
    }

    [dir="ltr"] .line-menu-btn:hover, [dir="ltr"] .line-menu-link:hover {
        transform: translateX(2px);
    }

    .line-menu-link.active-root, .line-menu-btn.active-parent {
        background: linear-gradient(135deg, rgba(106, 102, 157, 0.14) 0%, rgba(27, 50, 91, 0.08) 100%);
        border: 1px solid rgba(106, 102, 157, 0.22);
        color: #1B325B !important;
        font-weight: 700;
    }

    .line-icon-badge {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 0.5rem;
        background: #F3F5F8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6A669D;
        font-size: 1rem;
        transition: all 0.22s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .line-menu-link.active-root .line-icon-badge,
    .line-menu-btn.active-parent .line-icon-badge,
    .line-menu-btn:hover .line-icon-badge,
    .line-menu-link:hover .line-icon-badge {
        background: linear-gradient(135deg, #6A669D 0%, #1B325B 100%);
        color: #ffffff !important;
        box-shadow: 0 3px 8px rgba(106, 102, 157, 0.35);
    }

    .line-menu-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: inherit;
    }

    .line-menu-arrow {
        font-size: 0.72rem;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        color: #A1A5B7;
    }

    .line-menu-arrow.rotate-180 {
        transform: rotate(180deg);
        color: #6A669D;
    }

    .line-submenu {
        margin-inline-start: 1.15rem;
        padding-inline-start: 0.85rem;
        border-inline-start: 2px dashed rgba(106, 102, 157, 0.25);
        margin-top: 0.2rem;
        margin-bottom: 0.4rem;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .line-sub-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        color: #5E6278;
        font-size: 0.83rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .line-sub-item:hover {
        background: rgba(106, 102, 157, 0.08);
        color: #1B325B;
        padding-inline-start: 0.95rem;
    }

    .line-sub-dot {
        width: 6px;
        height: 6px;
        min-width: 6px;
        border-radius: 50%;
        background: #B5B5C3;
        transition: all 0.2s ease;
    }

    .line-sub-item:hover .line-sub-dot {
        background: #6A669D;
        transform: scale(1.3);
    }

    .line-sub-item.active-sub {
        background: #ffffff;
        color: #6A669D !important;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(106, 102, 157, 0.12);
        border: 1px solid rgba(106, 102, 157, 0.18);
    }

    .line-sub-item.active-sub .line-sub-dot {
        background: #6A669D;
        box-shadow: 0 0 6px #6A669D;
        transform: scale(1.4);
    }

    /* Dark Mode Support for Line Sidebar */
    [data-bs-theme="dark"] .line-menu-btn,
    [data-bs-theme="dark"] .line-menu-link {
        color: #CDD5DF;
    }
    [data-bs-theme="dark"] .line-icon-badge {
        background: #1e1e2d;
        color: #8E84BF;
    }
    [data-bs-theme="dark"] .line-sub-item {
        color: #92929F;
    }
    [data-bs-theme="dark"] .line-sub-item.active-sub {
        background: #1B2C4E;
        color: #9ABF80 !important;
        border-color: rgba(154, 191, 128, 0.3);
    }
    [data-bs-theme="dark"] .line-menu-link.active-root,
    [data-bs-theme="dark"] .line-menu-btn.active-parent {
        background: rgba(106, 102, 157, 0.22);
        color: #ffffff !important;
    }

    body {
        background-color: var(--bs-body-bg);
        color: var(--bs-text-primary);
        font-family: 'Cairo', sans-serif !important;
    }

    body, html {
        font-family: 'Cairo', sans-serif !important;
    }

    .container-xxl {
        max-width: 100% !important;
    }

    /* ── Light Mode Sidebar & Navigation ────────────────────────────── */
    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar,
    :root:not([data-bs-theme="dark"]) .notification-header {
        background-color: var(--bs-primary) !important;
        border-right: none;
    }

    .app-sidebar-logo,
    #kt_app_sidebar_logo,
    [data-kt-app-layout=dark-sidebar] .app-sidebar-logo {
        background-color: #ffffff !important;
    }

    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link .menu-title {
        color: #ffffff !important;
    }

    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link.active,
    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item.hover > .menu-link {
        background-color: var(--bs-primary-active) !important;
        color: #ffffff !important;
        border-radius: 8px;
    }

    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link:hover .menu-title {
        color: #ffffff !important;
    }

    /* ── Light Mode Base Elements & Contrast Protection ────────────── */
    :root:not([data-bs-theme="dark"]) h1,
    :root:not([data-bs-theme="dark"]) h2,
    :root:not([data-bs-theme="dark"]) h3,
    :root:not([data-bs-theme="dark"]) h4,
    :root:not([data-bs-theme="dark"]) h5,
    :root:not([data-bs-theme="dark"]) h6,
    :root:not([data-bs-theme="dark"]) .card-title {
        color: var(--bs-text-primary);
        font-weight: bold;
    }

    :root:not([data-bs-theme="dark"]) .symbol i,
    :root:not([data-bs-theme="dark"]) .symbol svg {
        color: var(--bs-primary);
    }

    /* Action Toolbar Buttons (.icon-btn & .btn-btc) */
    .icon-btn,
    .btn-btc {
        background-color: transparent !important;
        border: 1px solid #cbd5e0 !important;
        color: #4a5568 !important;
        border-radius: 6px !important;
        padding: 8px 10px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-decoration: none !important;
        line-height: 1 !important;
        vertical-align: middle !important;
    }

    .icon-btn i,
    .icon-btn svg,
    .btn-btc i,
    .btn-btc svg {
        color: #4a5568 !important;
        font-size: 14px !important;
        transition: color 0.3s ease !important;
    }

    .icon-btn:hover,
    .btn-btc:hover {
        background-color: #f1f5f9 !important;
        border-color: var(--bs-primary, #6A669D) !important;
        color: var(--bs-primary, #6A669D) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .icon-btn:hover i,
    .icon-btn:hover svg,
    .btn-btc:hover i,
    .btn-btc:hover svg {
        color: var(--bs-primary, #6A669D) !important;
    }

    /* Buttons: Text & Icons MUST be crisp white */
    .btn-primary,
    a.btn-primary {
        background-color: var(--bs-primary, #6A669D) !important;
        border-color: var(--bs-primary, #6A669D) !important;
        color: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.4rem !important;
    }

    .btn-primary *,
    .btn-primary i,
    .btn-primary svg,
    .btn-primary span,
    a.btn-primary i,
    a.btn-primary svg,
    a.btn-primary span {
        color: #ffffff !important;
    }

    .btn-primary:hover,
    a.btn-primary:hover {
        background-color: var(--bs-primary-active, #1B325B) !important;
        border-color: var(--bs-primary-active, #1B325B) !important;
        color: #ffffff !important;
    }

    .btn-primary:hover i,
    .btn-primary:hover svg,
    a.btn-primary:hover i,
    a.btn-primary:hover svg {
        color: #ffffff !important;
    }

    .btn-secondary,
    .btn-secondary *,
    .btn-search,
    .btn-search * {
        background-color: var(--bs-alert) !important;
        border-color: var(--bs-alert) !important;
        color: #ffffff !important;
    }

    .badge-success {
        background-color: var(--bs-secondary) !important;
        border-color: var(--bs-secondary) !important;
        color: #ffffff !important;
    }

    .badge-danger {
        background-color: var(--bs-alert) !important;
        border-color: var(--bs-alert) !important;
        color: #ffffff !important;
    }

    /* Sidebar Icons & Bullets */
    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu-icon,
    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu-icon i,
    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu-icon svg {
        color: #ffffff !important;
    }

    :root:not([data-bs-theme="dark"]) [data-kt-app-layout=dark-sidebar] .app-sidebar .menu-bullet .bullet {
        background-color: #ffffff !important;
    }

    :root:not([data-bs-theme="dark"]) .nav-tabs .nav-item:has(.active) {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
        color: white !important;
        padding: .5rem 1rem;
        border-radius: .375rem;
        font-weight: 500;
    }

    :root:not([data-bs-theme="dark"]) .nav-tabs .nav-item .active,
    :root:not([data-bs-theme="dark"]) .nav-tabs .nav-item:has(.active) * {
        color: white !important;
        text-align: center !important;
    }

    :root:not([data-bs-theme="dark"]) .card-header,
    :root:not([data-bs-theme="dark"]) table thead th {
        background-color: var(--bs-card) !important;
        border-bottom: 1px solid #e2e8f0;
        color: var(--bs-primary-active) !important;
        font-weight: 700;
    }

    :root:not([data-bs-theme="dark"]) .card-body,
    :root:not([data-bs-theme="dark"]) .card-footer {
        background-color: #ffffff;
    }

    :root:not([data-bs-theme="dark"]) .form-control,
    :root:not([data-bs-theme="dark"]) .form-select {
        border-color: #CED4DA;
        background-color: #ffffff;
        color: var(--bs-primary-active);
    }

    :root:not([data-bs-theme="dark"]) .form-control:focus,
    :root:not([data-bs-theme="dark"]) .form-select:focus,
    :root:not([data-bs-theme="dark"]) input:focus,
    :root:not([data-bs-theme="dark"]) select:focus,
    :root:not([data-bs-theme="dark"]) textarea:focus {
        border-color: #6A669D !important;
        box-shadow: 0 0 0 0.2rem rgba(106, 102, 157, 0.2) !important;
    }

    :root:not([data-bs-theme="dark"]) .form-control.form-control-solid:focus {
        background-color: #ffffff !important;
        border-color: #6A669D !important;
    }

    :root:not([data-bs-theme="dark"]) .border-primary {
        border-color: #6A669D !important;
    }

    :root:not([data-bs-theme="dark"]) .text-primary {
        color: #6A669D !important;
    }

    :root:not([data-bs-theme="dark"]) .bg-light-primary {
        background-color: rgba(106, 102, 157, 0.1) !important;
    }

    :root:not([data-bs-theme="dark"]) .input-group-text.bg-light-primary {
        background-color: rgba(106, 102, 157, 0.1) !important;
        border-color: #6A669D !important;
        color: #6A669D !important;
    }

    :root:not([data-bs-theme="dark"]) .form-check-input:checked,
    :root:not([data-bs-theme="dark"]) .form-check.form-check-solid .form-check-input:checked,
    :root:not([data-bs-theme="dark"]) .form-check-custom .form-check-input:checked,
    :root:not([data-bs-theme="dark"]) .form-switch.form-check-solid .form-check-input:checked,
    :root:not([data-bs-theme="dark"]) input[type="radio"]:checked,
    :root:not([data-bs-theme="dark"]) input[type="checkbox"]:checked {
        background-color: #6A669D !important;
        border-color: #6A669D !important;
    }

    /* Light Mode Select2 */
    :root:not([data-bs-theme="dark"]) .select2-container--bootstrap5.select2-container--focus .select2-selection,
    :root:not([data-bs-theme="dark"]) .select2-container--bootstrap5.select2-container--open .select2-selection,
    :root:not([data-bs-theme="dark"]) .select2-container--default.select2-container--focus .select2-selection,
    :root:not([data-bs-theme="dark"]) .select2-container--default.select2-container--open .select2-selection {
        border-color: #6A669D !important;
        box-shadow: 0 0 0 0.2rem rgba(106, 102, 157, 0.2) !important;
    }

    :root:not([data-bs-theme="dark"]) .select2-container--default .select2-dropdown .select2-results__options .select2-results__option {
        color: var(--bs-primary-active) !important;
    }
    :root:not([data-bs-theme="dark"]) .select2-container--default .select2-dropdown .select2-results__options .select2-results__option--highlighted,
    :root:not([data-bs-theme="dark"]) .select2-container--bootstrap5 .select2-dropdown .select2-results__options .select2-results__option--highlighted {
        background-color: #6A669D !important;
        color: white !important;
    }
    :root:not([data-bs-theme="dark"]) .select2-container--default .select2-dropdown .select2-results__options .select2-results__option--selected,
    :root:not([data-bs-theme="dark"]) .select2-container--bootstrap5 .select2-dropdown .select2-results__options .select2-results__option--selected {
        background-color: #6A669D !important;
        color: white !important;
    }
    :root:not([data-bs-theme="dark"]) .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--bs-primary-active) !important;
    }
    :root:not([data-bs-theme="dark"]) .select2-container--default .select2-selection__placeholder {
        color: var(--bs-primary-active) !important;
    }

    /* ── Global Blue Elimination & Primary Component Overrides ────── */
    .page-item.active .page-link,
    .pagination .page-item.active .page-link {
        background-color: #6A669D !important;
        border-color: #6A669D !important;
        color: #ffffff !important;
    }

    .badge.badge-light-primary,
    .badge-light-primary {
        background-color: rgba(106, 102, 157, 0.12) !important;
        color: #6A669D !important;
    }

    .symbol-label.bg-light-primary {
        background-color: rgba(106, 102, 157, 0.12) !important;
        color: #6A669D !important;
    }

    .symbol-label.bg-light-primary i,
    .symbol-label.bg-light-primary svg {
        color: #6A669D !important;
    }

    /* Flatpickr Datepicker */
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange,
    .flatpickr-day.selected.inRange,
    .flatpickr-day.startRange.inRange,
    .flatpickr-day.endRange.inRange,
    .flatpickr-day.selected:focus,
    .flatpickr-day.startRange:focus,
    .flatpickr-day.endRange:focus,
    .flatpickr-day.selected:hover,
    .flatpickr-day.startRange:hover,
    .flatpickr-day.endRange:hover,
    .flatpickr-day.selected.prevMonthDay,
    .flatpickr-day.startRange.prevMonthDay,
    .flatpickr-day.endRange.prevMonthDay,
    .flatpickr-day.selected.nextMonthDay,
    .flatpickr-day.startRange.nextMonthDay,
    .flatpickr-day.endRange.nextMonthDay {
        background: #6A669D !important;
        border-color: #6A669D !important;
        color: #ffffff !important;
    }

    .flatpickr-day.inRange {
        background: rgba(106, 102, 157, 0.15) !important;
        border-color: transparent !important;
        box-shadow: -5px 0 0 rgba(106, 102, 157, 0.15), 5px 0 0 rgba(106, 102, 157, 0.15) !important;
    }

    .flatpickr-months .flatpickr-month,
    .flatpickr-current-month .flatpickr-monthDropdown-months {
        color: var(--bs-primary-active) !important;
    }

    /* Progress Bars & Form Ranges */
    .progress-bar.bg-primary {
        background-color: #6A669D !important;
    }

    .form-range::-webkit-slider-thumb {
        background-color: #6A669D !important;
    }

    .form-range::-moz-range-thumb {
        background-color: #6A669D !important;
    }

    /* ── Dark Mode Engine ([data-bs-theme="dark"]) ─────────────────── */
    [data-bs-theme="dark"] {
        --bs-body-bg: #111827;
        --bs-body-color: #94a3b8;
        --bs-card-bg: #1e293b;
        --bs-border-color: #334155;
        --bs-text-primary: #f1f5f9;
        --bs-primary: #8E84BF;
        --bs-primary-active: #685E99;
        --bs-link-hover-color: #a5ce8d;
    }

    [data-bs-theme="dark"] body {
        background-color: #111827 !important;
        color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] .app-page,
    [data-bs-theme="dark"] .app-wrapper,
    [data-bs-theme="dark"] .app-main,
    [data-bs-theme="dark"] .app-content {
        background-color: #111827 !important;
    }

    /* Headings & Text in Dark Mode */
    [data-bs-theme="dark"] h1,
    [data-bs-theme="dark"] h2,
    [data-bs-theme="dark"] h3,
    [data-bs-theme="dark"] h4,
    [data-bs-theme="dark"] h5,
    [data-bs-theme="dark"] h6,
    [data-bs-theme="dark"] .card-title,
    [data-bs-theme="dark"] .text-dark,
    [data-bs-theme="dark"] .text-gray-900,
    [data-bs-theme="dark"] .text-gray-800,
    [data-bs-theme="dark"] .text-gray-700,
    [data-bs-theme="dark"] strong,
    [data-bs-theme="dark"] b {
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .text-gray-600,
    [data-bs-theme="dark"] .text-gray-500 {
        color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] .text-gray-400,
    [data-bs-theme="dark"] .text-muted {
        color: #64748b !important;
    }

    [data-bs-theme="dark"] label,
    [data-bs-theme="dark"] .form-label {
        color: #cbd5e1 !important;
        font-weight: 600;
    }

    /* ── High-Contrast Inline Color Overrides for Dark Mode ─────────── */
    [data-bs-theme="dark"] [style*="color: #1B325B"],
    [data-bs-theme="dark"] [style*="color:#1B325B"],
    [data-bs-theme="dark"] [style*="color: #1b325b"],
    [data-bs-theme="dark"] [style*="color:#1b325b"],
    [data-bs-theme="dark"] [style*="color: #1b365d"],
    [data-bs-theme="dark"] [style*="color:#1b365d"],
    [data-bs-theme="dark"] [style*="color: #1E2B50"],
    [data-bs-theme="dark"] [style*="color:#1E2B50"],
    [data-bs-theme="dark"] [style*="color: #334155"],
    [data-bs-theme="dark"] [style*="color:#334155"],
    [data-bs-theme="dark"] [style*="color: #1e293b"],
    [data-bs-theme="dark"] [style*="color:#1e293b"],
    [data-bs-theme="dark"] [style*="color: #0f172a"],
    [data-bs-theme="dark"] [style*="color:#0f172a"],
    [data-bs-theme="dark"] [style*="color: black"],
    [data-bs-theme="dark"] [style*="color: #000"],
    [data-bs-theme="dark"] [style*="color:#000"] {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] [style*="color: #685E99"],
    [data-bs-theme="dark"] [style*="color:#685E99"],
    [data-bs-theme="dark"] [style*="color: #6A669D"],
    [data-bs-theme="dark"] [style*="color:#6A669D"] {
        color: #c4bbf0 !important;
    }

    [data-bs-theme="dark"] [style*="color: #2D6A4F"],
    [data-bs-theme="dark"] [style*="color:#2D6A4F"],
    [data-bs-theme="dark"] [style*="color: #2e8b57"],
    [data-bs-theme="dark"] [style*="color:#2e8b57"] {
        color: #86B36B !important;
    }

    [data-bs-theme="dark"] [style*="color: #d97706"],
    [data-bs-theme="dark"] [style*="color:#d97706"],
    [data-bs-theme="dark"] [style*="color: #F59E0B"],
    [data-bs-theme="dark"] [style*="color:#F59E0B"] {
        color: #fbbf24 !important;
    }

    /* ── High-Contrast Badges & Translucent Fills in Dark Mode ─────────── */
    [data-bs-theme="dark"] [style*="background: rgba(27, 50, 91"],
    [data-bs-theme="dark"] [style*="background:rgba(27, 50, 91"] {
        background: rgba(142, 132, 191, 0.20) !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] [style*="background: rgba(104, 94, 153"],
    [data-bs-theme="dark"] [style*="background:rgba(104, 94, 153"] {
        background: rgba(104, 94, 153, 0.28) !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] [style*="background: rgba(134, 179, 107"],
    [data-bs-theme="dark"] [style*="background:rgba(134, 179, 107"] {
        background: rgba(134, 179, 107, 0.22) !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] [style*="background: rgba(217, 119, 6"],
    [data-bs-theme="dark"] [style*="background:rgba(217, 119, 6"] {
        background: rgba(217, 119, 6, 0.22) !important;
        color: #fbbf24 !important;
    }

    [data-bs-theme="dark"] .badge,
    [data-bs-theme="dark"] span.badge {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .badge.badge-light,
    [data-bs-theme="dark"] .badge-light-dark {
        background-color: #151e2e !important;
        color: #94a3b8 !important;
    }

    /* Cards in Dark Mode */
    [data-bs-theme="dark"] .card,
    [data-bs-theme="dark"] .dash-card,
    [data-bs-theme="dark"] .zatca-card,
    [data-bs-theme="dark"] .env-card {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    }

    [data-bs-theme="dark"] .card-header,
    [data-bs-theme="dark"] .dash-card-header,
    [data-bs-theme="dark"] .zatca-card-header {
        background-color: #182234 !important;
        border-bottom: 1px solid #334155 !important;
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .card-body,
    [data-bs-theme="dark"] .card-footer,
    [data-bs-theme="dark"] .zatca-card-body {
        background-color: #1e293b !important;
        color: #94a3b8 !important;
    }

    /* Forms & Inputs in Dark Mode */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .zatca-input {
        background-color: #151e2e !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus,
    [data-bs-theme="dark"] .zatca-input:focus {
        background-color: #151e2e !important;
        border-color: #685E99 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(104, 94, 153, 0.3) !important;
    }

    [data-bs-theme="dark"] .input-group-text {
        background-color: #182234 !important;
        border-color: #334155 !important;
        color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] .form-control::placeholder {
        color: #64748b !important;
    }

    /* Tables & DataTables in Dark Mode */
    [data-bs-theme="dark"] table,
    [data-bs-theme="dark"] .table {
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] table thead th,
    [data-bs-theme="dark"] .table thead th {
        background-color: #182234 !important;
        color: #f1f5f9 !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] table tbody tr td,
    [data-bs-theme="dark"] .table tbody tr td {
        background-color: transparent !important;
        color: #cbd5e1 !important;
        border-color: #283548 !important;
    }

    [data-bs-theme="dark"] .table-row-dashed tr {
        border-bottom-color: #283548 !important;
    }

    [data-bs-theme="dark"] .table-striped>tbody>tr:nth-of-type(odd)>* {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }

    [data-bs-theme="dark"] .table-hover>tbody>tr:hover>* {
        background-color: rgba(255, 255, 255, 0.04) !important;
    }

    /* DataTables in Dark Mode */
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length select,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter input {
        background-color: #151e2e !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
        border-radius: 6px;
        padding: 4px 8px;
    }

    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_info,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length label,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter label {
        color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #151e2e !important;
        border: 1px solid #334155 !important;
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #1B325B !important;
        border-color: #1B325B !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        background: #182234 !important;
        border-color: #334155 !important;
        color: #64748b !important;
    }

    [data-bs-theme="dark"] .dt-buttons .btn {
        background-color: #151e2e !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    /* Select2 in Dark Mode */
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single {
        background-color: #151e2e !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
        height: auto !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #64748b !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-dropdown {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #151e2e !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-results__options .select2-results__option {
        color: #cbd5e1 !important;
        background-color: transparent !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-results__options .select2-results__option--highlighted {
        background-color: #1B325B !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .select2-container--default .select2-results__options .select2-results__option--selected {
        background-color: #283548 !important;
        color: #8E84BF !important;
    }

    /* Modals in Dark Mode */
    [data-bs-theme="dark"] .modal-content {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .modal-header,
    [data-bs-theme="dark"] .modal-footer {
        background-color: #182234 !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .modal-title {
        color: #f1f5f9 !important;
    }

    /* Dropdowns & Menus in Dark Mode */
    [data-bs-theme="dark"] .dropdown-menu,
    [data-bs-theme="dark"] .menu-sub-dropdown {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
    }

    [data-bs-theme="dark"] .menu-link {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .menu-link:hover,
    [data-bs-theme="dark"] .menu-link.active {
        background-color: #182234 !important;
        color: #f1f5f9 !important;
    }

    /* Accordions in Dark Mode */
    [data-bs-theme="dark"] .accordion-item {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .accordion-button {
        background-color: #182234 !important;
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
        background-color: #1e293b !important;
        color: #8E84BF !important;
    }

    [data-bs-theme="dark"] .accordion-body {
        background-color: #1e293b !important;
        color: #cbd5e1 !important;
    }

    /* Sidebar Logo & Navigation in Dark Mode */
    [data-bs-theme="dark"] [data-kt-app-layout=dark-sidebar] .app-sidebar-logo,
    [data-bs-theme="dark"] .app-sidebar-logo,
    [data-bs-theme="dark"] #kt_app_sidebar_logo {
        background-color: #111827 !important;
    }

    [data-bs-theme="dark"] #kt_app_header {
        background-color: #111827 !important;
        border-bottom: 1px solid #334155 !important;
    }

    [data-bs-theme="dark"] #kt_app_sidebar {
        background-color: #111827 !important;
        border-inline-end: 1px solid #334155 !important;
    }

    [data-bs-theme="dark"] #kt_app_footer {
        background-color: #111827 !important;
        border-top: 1px solid #334155 !important;
    }

    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .border-bottom,
    [data-bs-theme="dark"] .border-top,
    [data-bs-theme="dark"] .border-end,
    [data-bs-theme="dark"] .border-start,
    [data-bs-theme="dark"] .border-gray-100,
    [data-bs-theme="dark"] .border-gray-200,
    [data-bs-theme="dark"] .border-gray-300 {
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .bg-white,
    [data-bs-theme="dark"] .bg-body {
        background-color: #1e293b !important;
    }

    [data-bs-theme="dark"] .bg-light {
        background-color: #151e2e !important;
    }

    /* Buttons in Dark Mode */
    [data-bs-theme="dark"] .btn-light {
        background-color: #151e2e !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .btn-light:hover {
        background-color: #283548 !important;
        color: #ffffff !important;
    }

    /* Action Toolbar Buttons in Dark Mode */
    [data-bs-theme="dark"] .icon-btn,
    [data-bs-theme="dark"] .btn-btc {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .icon-btn i,
    [data-bs-theme="dark"] .icon-btn svg,
    [data-bs-theme="dark"] .btn-btc i,
    [data-bs-theme="dark"] .btn-btc svg {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .icon-btn:hover,
    [data-bs-theme="dark"] .btn-btc:hover {
        background-color: #283548 !important;
        border-color: #685E99 !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .icon-btn:hover i,
    [data-bs-theme="dark"] .icon-btn:hover svg,
    [data-bs-theme="dark"] .btn-btc:hover i,
    [data-bs-theme="dark"] .btn-btc:hover svg {
        color: #ffffff !important;
    }

    /* Pagination in Dark Mode */
    [data-bs-theme="dark"] .pagination .page-link {
        background-color: #151e2e !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .pagination .page-item.active .page-link {
        background-color: #1B325B !important;
        border-color: #1B325B !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .pagination .page-item.disabled .page-link {
        background-color: #182234 !important;
        border-color: #334155 !important;
        color: #64748b !important;
    }

    /* SweetAlert2 & Modals in Dark Mode */
    [data-bs-theme="dark"] .swal2-popup {
        background-color: #1e293b !important;
        color: #f1f5f9 !important;
        border: 1px solid #334155 !important;
    }

    [data-bs-theme="dark"] .swal2-title {
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .swal2-html-container {
        color: #cbd5e1 !important;
    }

    /* Datepicker & Flatpickr in Dark Mode */
    [data-bs-theme="dark"] .flatpickr-calendar,
    [data-bs-theme="dark"] .daterangepicker {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .flatpickr-day,
    [data-bs-theme="dark"] .daterangepicker td {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .flatpickr-day:hover,
    [data-bs-theme="dark"] .daterangepicker td:hover {
        background-color: #1B325B !important;
        color: #ffffff !important;
    }

    /* Print Styles */
    @media print {
        .btn, .icon-btn, .card-footer, .no-print, button, #card-filter, .table-action {
            display: none !important;
        }
    }

    /* ==========================================================================
       Responsive Tables & Clean Mobile Adapters
       ========================================================================== */
    /* Tables: smooth touch scrolling on smaller screens */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.03);
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: rgba(106, 102, 157, 0.25);
        border-radius: 10px;
    }

    table.table,
    table.dataTable {
        width: 100% !important;
        margin-bottom: 0 !important;
        vertical-align: middle;
    }

    table th, table td {
        white-space: normal;
        vertical-align: middle;
    }

    .table td .badge,
    .table th .badge,
    .table-action,
    .table .btn-icon,
    .table .btn-group {
        white-space: nowrap !important;
    }

    /* DataTables Responsive Controls */
    .dataTables_wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .dataTables_wrapper .row {
        align-items: center;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    /* Prevent wide elements from breaking mobile */
    .card,
    .dash-card,
    .zatca-card,
    .env-card {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Small & Medium Screens (< 992px) */
    @media (max-width: 991.98px) {
        .app-container,
        .container-xxl,
        .container-fluid,
        #kt_app_content_container {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            max-width: 100% !important;
        }

        .card {
            margin-bottom: 1rem;
        }

        .card-body,
        .card-header,
        .card-footer {
            padding: 1rem !important;
        }

        /* Action toolbars wrap and align */
        .app-toolbar #kt_app_toolbar_container {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        .app-toolbar .page-title {
            margin-bottom: 0.25rem !important;
        }

        .app-toolbar .d-flex.align-items-center.gap-2,
        .app-toolbar .d-flex.align-items-center.gap-lg-3 {
            flex-wrap: wrap !important;
            width: 100% !important;
            justify-content: flex-start !important;
            gap: 0.5rem !important;
        }

        .icon-btn,
        .btn-btc {
            padding: 6px 9px !important;
            font-size: 0.85rem !important;
        }

        .btn-primary {
            padding: 0.5rem 0.85rem !important;
            font-size: 0.85rem !important;
        }

        /* Modals & SweetAlerts */
        .modal-dialog {
            margin: 0.5rem auto !important;
            max-width: calc(100vw - 1rem) !important;
        }

        .swal2-popup {
            width: 90vw !important;
            padding: 1rem !important;
        }

        /* Table font size scaling */
        table th, table td {
            font-size: 0.825rem !important;
            padding: 0.5rem 0.4rem !important;
        }
    }

    /* Small Screens (Smartphones < 768px) */
    @media (max-width: 767.98px) {
        .dataTables_wrapper .dataTables_filter {
            width: 100% !important;
            text-align: right !important;
            margin-top: 0.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            display: block !important;
        }

        .dataTables_wrapper .dataTables_length {
            width: 100% !important;
            margin-bottom: 0.5rem !important;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            width: 100% !important;
            text-align: center !important;
            justify-content: center !important;
            margin-top: 0.5rem !important;
        }

        .dataTables_wrapper .pagination {
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 2px !important;
        }

        .dataTables_wrapper .pagination .page-link {
            padding: 0.35rem 0.6rem !important;
            font-size: 0.8rem !important;
        }
    }

    /* Extra Small Screens (Smartphones < 576px) */
    @media (max-width: 575.98px) {
        .row.g-3 {
            --bs-gutter-x: 0.5rem !important;
            --bs-gutter-y: 0.5rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .page-heading {
            font-size: 1.15rem !important;
        }

        .kpi-num {
            font-size: 1.15rem !important;
        }

        .quick-access-card {
            padding: 0.75rem !important;
        }

        .quick-access-title {
            font-size: 0.825rem !important;
        }

        .quick-access-subtitle {
            font-size: 0.7rem !important;
        }

        .d-flex.align-items-center.gap-2 button,
        .d-flex.align-items-center.gap-2 a {
            flex-grow: 1;
            text-align: center;
            justify-content: center;
        }
    }

    /* Desktop & Ultra-wide (>= 1200px) */
    @media (min-width: 1200px) {
        .dashboard-container {
            max-width: 100% !important;
        }
    }
</style>




{{-- <style>
    :root {
        --bs-primary: #4bd5b5;
        --bs-text-primary: #4bd5b5;
        --bs-primary-active: #4f7d82;
        --bs-link-hover-color: #4f7d82;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link.active .menu-title {
        color: #4bd5b5;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar-logo {
        background-color: #fff;
    }



    @media print {

        .btn,
        .no-print {
            display: none !important;
        }


    }





    /*
    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link.active .menu-title {
        color: #1b3639;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link.active {
        transition: color .2s ease;
        background-color: #ffffff;
        color: #f5f5f5;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar {
        background-color: #4bd5b6;
        border-right: 0;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item .menu-link .menu-title {
        color: #233e41;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item.hover:not(.here)>.menu-link:not(.disabled):not(.active):not(.here),
    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item:not(.here) .menu-link:hover:not(.disabled):not(.active):not(.here) {
        transition: color .2s ease;
        color: #233e41;
    }

    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item.hover:not(.here)>.menu-link:not(.disabled):not(.active):not(.here) .menu-title,
    [data-kt-app-layout=dark-sidebar] .app-sidebar .menu .menu-item:not(.here) .menu-link:hover:not(.disabled):not(.active):not(.here) .menu-title {
        color: #fff;
    }

    i.bi,
    i[class*=" fa-"],
    i[class*=" fonticon-"],
    i[class*=" la-"],
    i[class^=fa-],
    i[class^=fonticon-],
    i[class^=la-] {
        line-height: 1;
        font-size: 1rem;
        color: var(--bs-primary-active);
    } */

    /* --bs-primary:#4bd5b5 */
    /* --bs-link-hover-color: #2aa085; */
</style> --}}
<!--end::Global Stylesheets Bundle-->
<script>
    // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
</script>

@livewireStyles
