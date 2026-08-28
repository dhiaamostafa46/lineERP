@extends('layouts.app')

@section('title', __('مراجعة وتدقيق شجرة الحسابات المستوردة'))

@section('content')
@php
    $accountTypes = \App\Models\AccuSoft\TreeAccounts::accountTypes();
    $natures = \App\Models\AccuSoft\TreeAccounts::types();
    
    $totalCount = count($tree);
    $errorCount = 0;
    foreach ($tree as $node) {
        if (!empty($node['errors'])) {
            $errorCount++;
        }
    }
    $readyCount = $totalCount - $errorCount;
@endphp

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    مراجعة وتدقيق شجرة الحسابات المستوردة
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <form action="{{ route('accusoft.TreeAccounts.importCancel') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">
                        <i class="fas fa-trash-alt"></i> إلغاء وتراجع
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!-- Summary Cards -->
            <div class="row g-5 mb-10">
                <div class="col-md-4">
                    <div class="card shadow-sm border-start border-primary border-4">
                        <div class="card-body py-5">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fw-bold d-block">إجمالي الحسابات المعالجة</span>
                                    <span class="fs-2hx fw-bold text-gray-800">{{ $totalCount }}</span>
                                </div>
                                <div class="bg-light-primary p-3 rounded">
                                    <i class="fas fa-list fs-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-start border-success border-4">
                        <div class="card-body py-5">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fw-bold d-block">حسابات جاهزة للاستيراد</span>
                                    <span class="fs-2hx fw-bold text-gray-800">{{ $readyCount }}</span>
                                </div>
                                <div class="bg-light-success p-3 rounded">
                                    <i class="fas fa-check-circle fs-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-start border-danger border-4">
                        <div class="card-body py-5">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted fw-bold d-block">حسابات تحتوي على أخطاء</span>
                                    <span class="fs-2hx fw-bold text-gray-800">{{ $errorCount }}</span>
                                </div>
                                <div class="bg-light-danger p-3 rounded">
                                    <i class="fas fa-exclamation-triangle fs-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Tree Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold text-gray-800">معاينة الهيكلية الجديدة لرموز النظام</h3>
                    <div>
                        @if ($errorCount > 0)
                            <button class="btn btn-success" disabled title="يرجى إصلاح الأخطاء لتتمكن من التأكيد">
                                <i class="fas fa-save"></i> تأكيد وحفظ الشجرة
                            </button>
                        @else
                            <form action="{{ route('accusoft.TreeAccounts.importConfirm') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> تأكيد وحفظ الشجرة في النظام
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-row-bordered table-row-gray-300 align-middle gs-9 gy-4 mb-0">
                            <thead>
                                <tr class="fw-bold fs-6 text-gray-800 bg-light">
                                    <th>الرمز الجديد المعين</th>
                                    <th>اسم الحساب (عربي)</th>
                                    <th>اسم الحساب (انجليزي)</th>
                                    <th>تصنيف الحساب</th>
                                    <th>طبيعة الحساب</th>
                                    <th>الرابط بالـ Mapping</th>
                                    <th>الحساب الأب</th>
                                    <th>الحالة / الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tree as $node)
                                    @php
                                        // Simple heuristic to highlight mapping rows
                                        $isMapped = false;
                                        $mappingLabel = '-';
                                        
                                        $mappingKeysMap = [
                                            'customer' => 'العملاء',
                                            'sales' => 'المبيعات',
                                            'sales_return' => 'مردودات المبيعات',
                                            'sales_discount' => 'الخصم المسموح به',
                                            'sales_tax' => 'ضريبة المبيعات',
                                            'shipping_revenue' => 'إيرادات شحن',
                                            'sales_inventory' => 'مخزون إنتاج تام',
                                            'cogs' => 'تكلفة البضاعة المباعة',
                                            'supplier' => 'الموردين',
                                            'purchase_inventory' => 'مخزون مواد أولية',
                                            'purchase' => 'المشتريات',
                                            'purchase_return' => 'مردودات المشتريات',
                                            'purchase_discount' => 'الخصم المكتسب',
                                            'purchase_tax' => 'ضريبة المشتريات',
                                            'inventory' => 'المخزون',
                                            'inventory_in_transit' => 'بضاعة بالطريق',
                                            'inventory_settlement' => 'تسوية المخزون',
                                            'inventory_damage' => 'تلف وفاقد المخزون',
                                            'cash' => 'الصندوق الرئيسي',
                                            'bank' => 'حساب بنكي جاري',
                                            'tax' => 'حساب الضريبة',
                                            'capital' => 'رأس المال',
                                            'retained_earnings' => 'أرباح وخسائر مرحلة',
                                            'salaries_expense' => 'الرواتب الأساسية',
                                            'accrued_salaries' => 'رواتب وأجور مستحقة',
                                            'accumulated_depreciation' => 'مجمعات الإهلاك',
                                        ];

                                        $normAr = trim(preg_replace('/[\x{064B}-\x{0652}]/u', '', $node['name_ar']));
                                        $normAr = preg_replace('/[أإآ]/u', 'ا', $normAr);
                                        $normAr = preg_replace('/ى/u', 'ي', $normAr);
                                        $normAr = preg_replace('/ة/u', 'ه', $normAr);
                                        $normAr = mb_strtolower(preg_replace('/\s+/', ' ', $normAr), 'UTF-8');

                                        foreach ($mappingKeysMap as $k => $v) {
                                            $normMappingName = preg_replace('/[\x{064B}-\x{0652}]/u', '', $v);
                                            $normMappingName = preg_replace('/[أإآ]/u', 'ا', $normMappingName);
                                            $normMappingName = preg_replace('/ى/u', 'ي', $normMappingName);
                                            $normMappingName = preg_replace('/ة/u', 'ه', $normMappingName);
                                            $normMappingName = mb_strtolower(preg_replace('/\s+/', ' ', $normMappingName), 'UTF-8');

                                            if ($normAr === $normMappingName) {
                                                $isMapped = true;
                                                $mappingLabel = $v;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ !empty($node['errors']) ? 'bg-light-danger' : ($isMapped ? 'bg-light-success' : '') }}">
                                        <td>
                                            <span class="badge badge-light-dark fw-bold font-monospace fs-6">{{ $node['code'] ?? '-' }}</span>
                                        </td>
                                        <td style="padding-right: {{ ($node['level'] - 1) * 20 }}px;">
                                            @if ($node['level'] > 1)
                                                <span class="text-muted me-1">├──</span>
                                            @endif
                                            <span class="fw-bold text-gray-800">{{ $node['name_ar'] }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $node['name_en'] ?: '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ $accountTypes[$node['account_type']] ?? 'غير معروف' }}</span>
                                        </td>
                                        <td>
                                            @if ($node['type'] == 1)
                                                <span class="text-success fw-semibold"><i class="fas fa-plus-circle text-success me-1"></i> مدين</span>
                                            @else
                                                <span class="text-danger fw-semibold"><i class="fas fa-minus-circle text-danger me-1"></i> دائن</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($isMapped)
                                                <span class="badge badge-light-success fw-bold border border-success"><i class="fas fa-link text-success me-1"></i> {{ $mappingLabel }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-gray-600">{{ $node['parent_name'] ?: '-' }}</span>
                                        </td>
                                        <td>
                                            @if (!empty($node['errors']))
                                                @foreach ($node['errors'] as $err)
                                                    <div class="text-danger small fw-bold"><i class="fas fa-times-circle me-1"></i> {{ $err }}</div>
                                                @endforeach
                                            @elseif (!empty($node['already_exists']))
                                                <span class="badge badge-light-warning"><i class="fas fa-info-circle me-1"></i> موجود مسبقاً (لن يتم تكراره)</span>
                                            @else
                                                <span class="badge badge-light-success"><i class="fas fa-check me-1"></i> جاهز للاستيراد</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
