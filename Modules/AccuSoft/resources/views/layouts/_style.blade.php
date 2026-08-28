<style>
    #AS-incomeStatement-table {
        margin: 0;
        background-color: #ffffff;
    }

    #AS-incomeStatement-table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        padding: 12px 16px;
        color: #495057;
    }

    #AS-incomeStatement-table tbody td,
    #AS-incomeStatement-table tfoot td {
        padding: 10px 16px;
        border: 1px solid #e9ecef;
        color: #212529;
    }

    /* عناوين الأقسام */
    .section-header td {
        font-size: 1.05rem;
        padding: 12px 16px;
        border-top: 2px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
    }

    .revenue-header td {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .expense-header td {
        background-color: #f8d7da;
        color: #842029;
    }

    /* صفوف الحسابات */
    .account-row {
        transition: background-color 0.2s;
    }

    .account-row.hidden {
        display: none;
    }

    .account-row:hover {
        background-color: #f8f9fa;
    }

    .parent-account {
        cursor: pointer;
        font-weight: 500;
    }

    .leaf-row {
        font-weight: normal;
    }

    .account-code {
        color: #6c757d;
        font-size: 0.9rem;
        margin-right: 5px;
    }

    /* أيقونة التوسيع/الطي */
    .toggle-icon {
        transition: transform 0.3s;
        font-size: 11px;
        color: #0d6efd;
    }

    .toggle-icon.collapsed {
        transform: rotate(-90deg);
    }

    /* صفوف الإجماليات */
    .total-row td {
        padding: 12px 16px;
        font-size: 1.05rem;
        border-top: 2px solid #dee2e6;
    }

    .revenue-total td {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .expense-total td {
        background-color: #f8d7da;
        color: #842029;
    }

    /* صافي الدخل */
    .net-income-row td {
        padding: 14px 16px;
        font-size: 1.1rem;
        border-top: 3px solid #dee2e6;
    }

    .net-income-row.profit td {
        background-color: #198754;
        color: #ffffff;
    }

    .net-income-row.loss td {
        background-color: #dc3545;
        color: #ffffff;
    }

    /* RTL Support */
    [dir="rtl"] .toggle-icon {
        margin-right: 8px;
        margin-left: 0;
    }

    [dir="rtl"] .account-code {
        margin-left: 5px;
        margin-right: 0;
    }

    /* طباعة */
    @media print {
        @page {
            margin: 1cm;
        }

        body, html {
            width: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
            background: #fff !important;
        }

        /* Fix container constraints */
        .app-main, .app-wrapper, .app-page, .app-container, .container, .container-fluid, .container-xxl {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Hide elements not meant for print */
        .app-header, .app-sidebar, .app-toolbar, #card-filter, .d-print-none {
            display: none !important;
        }

        /* Fix table overflow and scrolling which hides data */
        .table-responsive {
            overflow: visible !important;
        }

        .table {
            width: 100% !important;
            max-width: 100% !important;
            border-collapse: collapse !important;
        }

        .table th, .table td {
            white-space: normal !important;
            padding: 4px !important;
            font-size: 11pt !important;
        }

        /* Card styles reset for print */
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-body {
            padding: 0 !important;
        }

        .toggle-icon {
            display: none;
        }

        .account-row {
            page-break-inside: avoid;
        }
    }
</style>
