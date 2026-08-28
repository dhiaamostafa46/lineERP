@extends('layouts.app')

@section('title', 'تشغيل دورة إهلاك جديدة')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    تشغيل دورة إهلاك مجمعة
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.depreciation_runs.index') }}" class="btn btn-sm btn-secondary">العودة لدورات الإهلاك</a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('flash::message')
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('accusoft.depreciation_runs.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-5">
                                <label class="form-label required">الشهر</label>
                                <select name="run_month" class="form-select form-select-solid" required>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-5">
                                <label class="form-label required">السنة</label>
                                <select name="run_year" class="form-select form-select-solid" required>
                                    @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                        <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-5">
                                <label class="form-label">ملاحظات (اختياري)</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-12 mb-5">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="uses_individual_entries" value="1" id="uses_individual_entries" />
                                    <label class="form-check-label" for="uses_individual_entries">
                                        إنشاء قيد محاسبي مستقل لكل أصل (بدلاً من قيد مجمع واحد)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-warning fs-2hx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-warning">تنبيه</h4>
                                <span>سيتم احتساب الإهلاك لجميع الأصول النشطة عن الشهر المحدد تلقائياً وإنشاء قيد محاسبي مجمع بقيمة الإهلاكات. يرجى التأكد من أن الإهلاك لم يتم تنفيذه مسبقاً لهذا الشهر.</span>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('هل أنت متأكد من تنفيذ دورة الإهلاك المجمعة؟')">
                                تشغيل واعتماد الإهلاك آلياً
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
