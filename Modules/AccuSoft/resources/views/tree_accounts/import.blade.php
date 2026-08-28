@extends('layouts.app')

@section('title', __('استيراد ومراجعة شجرة الحسابات'))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    استيراد ومراجعة شجرة الحسابات الذكي
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.TreeAccounts.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-right"></i> @lang('lang.back')
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10 border border-primary">
                <i class="fas fa-magic fs-2hx text-primary me-4 mb-5 mb-sm-0"></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <h4 class="fw-bold text-primary">المعالجة الذكية لشجرة الحسابات</h4>
                    <span>يقوم النظام بتجاهل الأكواد القديمة وبناء هيكلية الحسابات والـ parent_id تلقائياً بناءً على <strong>الاسم</strong> و<strong>النوع</strong> و<strong>الهرمية</strong>. بعد ذلك يتم توليد أكواد نظام جديدة وتوزيع التوجيهات المحاسبية تلقائياً.</span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="card-title">
                        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-active-primary active" data-bs-toggle="tab" data-bs-target="#tab_file" type="button" role="tab">
                                    <i class="fas fa-file-excel me-2"></i> ملف Excel / CSV
                                </button>
                            </li>
                            <!-- <li class="nav-item" role="presentation">
                                <button class="nav-link text-active-primary" data-bs-toggle="tab" data-bs-target="#tab_api" type="button" role="tab">
                                    <i class="fas fa-cloud-download-alt me-2"></i> رابط API خارجي (JSON/XML)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-active-primary" data-bs-toggle="tab" data-bs-target="#tab_manual" type="button" role="tab">
                                    <i class="fas fa-keyboard me-2"></i> إدخال يدوي (JSON/CSV)
                                </button>
                            </li> -->
                        </ul>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        
                        <!-- Tab 1: File -->
                        <div class="tab-pane fade show active" id="tab_file" role="tabpanel">
                            <form action="{{ route('accusoft.TreeAccounts.importProcess') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="source_type" value="file">
                                <div class="mb-8">
                                    <label class="form-label fw-bold fs-6 required">اختر ملف الحسابات</label>
                                    <input type="file" name="file" class="form-control form-control-solid" required accept=".xlsx, .xls, .csv">
                                    <div class="form-text text-muted">يدعم التنسيقات .xlsx, .xls, .csv. تأكد من احتواء الملف على أعمدة الاسم والنوع والاب.</div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-cogs"></i> معالجة ومراجعة
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab 2: API -->
                        <div class="tab-pane fade" id="tab_api" role="tabpanel">
                            <form action="{{ route('accusoft.TreeAccounts.importProcess') }}" method="POST">
                                @csrf
                                <input type="hidden" name="source_type" value="api">
                                <div class="row mb-8">
                                    <div class="col-md-8 mb-4">
                                        <label class="form-label fw-bold fs-6 required">رابط الـ API الخارجي</label>
                                        <input type="url" name="api_url" class="form-control form-control-solid" placeholder="https://api.example.com/chart-of-accounts" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold fs-6 required">تنسيق البيانات المتوقع</label>
                                        <select name="api_format" class="form-select form-select-solid" required>
                                            <option value="json">JSON</option>
                                            <option value="xml">XML</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-cloud-download-alt"></i> جلب ومعالجة
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab 3: Manual -->
                        <div class="tab-pane fade" id="tab_manual" role="tabpanel">
                            <form action="{{ route('accusoft.TreeAccounts.importProcess') }}" method="POST">
                                @csrf
                                <input type="hidden" name="source_type" value="manual">
                                <div class="row mb-8">
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label fw-bold fs-6 required">تنسيق المدخلات</label>
                                        <select name="manual_format" class="form-select form-select-solid" onchange="updatePlaceholder(this.value)" required>
                                            <option value="manual_json">JSON Array</option>
                                            <option value="manual_csv">CSV String</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold fs-6 required">ألصق البيانات هنا</label>
                                        <textarea id="manual_data_textarea" name="manual_data" rows="10" class="form-control form-control-solid font-monospace" placeholder="" required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check-double"></i> معالجة ومراجعة البيانات
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="text-muted small">تعتمد الأعمدة في التفسير التلقائي على الأسماء مثل: (الاسم، نوع الحساب، طبيعة الحساب، الحساب الأب)</span>
                    <a href="{{ route('accusoft.TreeAccounts.downloadTemplate') }}" class="btn btn-sm btn-outline btn-outline-success">
                        <i class="fas fa-file-excel"></i> تحميل نموذج الإكسيل
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
    function updatePlaceholder(value) {
        var textarea = document.getElementById('manual_data_textarea');
        if (value === 'manual_json') {
            textarea.placeholder = '[\n  {\n    "name_ar": "العملاء"،\n    "account_type": "العملاء"،\n    "type": "مدين"\n  },\n  {\n    "name_ar": "عملاء محليون"،\n    "account_type": "العملاء"،\n    "type": "مدين"،\n    "parent_name": "العملاء"\n  }\n]';
        } else {
            textarea.placeholder = 'name_ar,account_type,type,parent_name\nالعملاء,العملاء,مدين,\nعملاء محليون,العملاء,مدين,العملاء';
        }
    }
    // Set default
    document.addEventListener("DOMContentLoaded", function() {
        updatePlaceholder('manual_json');
    });
</script>
@endsection
