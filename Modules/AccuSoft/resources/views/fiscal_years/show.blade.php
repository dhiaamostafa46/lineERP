@extends('layouts.app')

@section('title', __('accusoft::models/as_fiscal_years.singular') . ' - ' . ($fiscalYear->name ?? ''))

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                   @lang('crud.detail') @lang('accusoft::models/as_fiscal_years.singular'): {{ $fiscalYear->name }}
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('accusoft.FiscalYear.index') }}" class="text-muted text-hover-primary">
                            @lang('accusoft::models/as_fiscal_years.plural')
                        </a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ $fiscalYear->name }}</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->

            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if (!$fiscalYear->is_closed)
                    <a href="{{ route('accusoft.FiscalYear.edit', $fiscalYear->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-edit me-1"></i> @lang('lang.edit')
                    </a>
                    {!! Form::open(['route' => ['accusoft.FiscalYear.close', $fiscalYear->id], 'method' => 'get', 'class' => 'd-inline']) !!}
                    {!! Form::button('<i class="fa-solid fa-calculator me-1"></i> إقفال السنة المالية', [
                        'type' => 'submit',
                        'class' => 'btn btn-sm btn-danger',
                        'onclick' => "return confirm('هل أنت متأكد من رغبتك في إقفال السنة المالية؟ سيتم تصفير حسابات الإيرادات والمصروفات ونقل النتيجة وتدوير الأرصدة.')",
                    ]) !!} 
                    {!! Form::close() !!}
                @else
                    {!! Form::open(['route' => ['accusoft.FiscalYear.reopen', $fiscalYear->id], 'method' => 'get', 'class' => 'd-inline']) !!}
                    {!! Form::button('<i class="fa-solid fa-lock-open me-1"></i> إلغاء الإقفال وإعادة الفتح', [
                        'type' => 'submit',
                        'class' => 'btn btn-sm btn-warning',
                        'onclick' => "return confirm('هل أنت متأكد من رغبتك في إلغاء إقفال السنة المالية وحذف قيود الإقفال وإعادة فتحها للتعديل؟')",
                    ]) !!} 
                    {!! Form::close() !!}
                @endif
                <a class="btn btn-sm btn-secondary" href="{{ route('accusoft.FiscalYear.index') }}">
                    <i class="fa-solid fa-arrow-left me-1"></i> @lang('crud.back')
                </a>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <!--begin::KPI Stats Row-->
            <div class="row g-5 g-xl-8 mb-5">
                <!-- Total Revenues -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-300 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold fs-7">إجمالي الإيرادات (5xxx)</div>
                                    <div class="text-gray-900 fw-bold fs-2qx mt-1 text-success">
                                        {{ number_format($totalRevenues, 2) }} <span class="fs-7 text-muted">ر.س</span>
                                    </div>
                                </div>
                                <div class="symbol symbol-50px symbol-circle bg-light-success p-3">
                                    <i class="fa-solid fa-arrow-trend-up fs-2 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-300 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold fs-7">المصروفات والتكلفة (4xxx)</div>
                                    <div class="text-gray-900 fw-bold fs-2qx mt-1 text-danger">
                                        {{ number_format($totalExpenses, 2) }} <span class="fs-7 text-muted">ر.س</span>
                                    </div>
                                </div>
                                <div class="symbol symbol-50px symbol-circle bg-light-danger p-3">
                                    <i class="fa-solid fa-arrow-trend-down fs-2 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Result -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-300 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold fs-7">
                                        صافي النتيجة ({{ $netIncome >= 0 ? 'صافي ربح' : 'صافي خسارة' }})
                                    </div>
                                    <div class="text-gray-900 fw-bold fs-2qx mt-1 {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($netIncome), 2) }} <span class="fs-7 text-muted">ر.س</span>
                                    </div>
                                </div>
                                <div class="symbol symbol-50px symbol-circle {{ $netIncome >= 0 ? 'bg-light-success' : 'bg-light-danger' }} p-3">
                                    <i class="fa-solid {{ $netIncome >= 0 ? 'fa-scale-balanced text-success' : 'fa-triangle-exclamation text-danger' }} fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Entries -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8 border border-gray-300 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-gray-500 fw-semibold fs-7">القيود المرحلة</div>
                                    <div class="text-gray-900 fw-bold fs-2qx mt-1 text-primary">
                                        {{ number_format($entriesStats['posted']) }} <span class="fs-7 text-muted">قيد</span>
                                    </div>
                                </div>
                                <div class="symbol symbol-50px symbol-circle bg-light-primary p-3">
                                    <i class="fa-solid fa-file-invoice fs-2 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::KPI Stats Row-->

            <!--begin::Basic Details Card-->
            <div class="card shadow-sm mb-5">
                <div class="card-header border-0 pt-4">
                    <h3 class="card-title fw-bold text-gray-800">
                        <i class="fa-solid fa-circle-info text-primary me-2"></i>
                        البيانات الأساسية للسنة المالية
                    </h3>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        @include('accusoft::fiscal_years.show_fields')
                    </div>
                </div>
            </div>
            <!--end::Basic Details Card-->

            <!--begin::Closing Journal Entries Section (If Closed)-->
            @if ($fiscalYear->is_closed && !empty($closingEntries) && $closingEntries->count() > 0)
                <div class="card shadow-sm border border-success">
                    <div class="card-header border-0 pt-4 bg-light-success rounded-top">
                        <h3 class="card-title fw-bold text-success">
                            <i class="fa-solid fa-lock text-success me-2"></i>
                            قيود الإقفال المحاسبي المنشأة للسنة المالية (Closing Entries)
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-striped table-hover gy-5 gs-7 mb-0">
                                <thead>
                                    <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-300 bg-light">
                                        <th>رقم القيد</th>
                                        <th>التاريخ</th>
                                        <th>البيان والوصف</th>
                                        <th class="text-end">المدين</th>
                                        <th class="text-end">الدائن</th>
                                        <th class="text-center">المستخدم</th>
                                        <th class="text-center">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($closingEntries as $closingEntry)
                                        <tr>
                                            <td class="fw-bold">
                                                <a href="{{ route('accusoft.JournalEntry.show', $closingEntry->id) }}" class="text-primary fw-bold text-hover-underline">
                                                    {{ $closingEntry->entry_number }}
                                                </a>
                                            </td>
                                            <td>{{ $closingEntry->entry_date ? \Carbon\Carbon::parse($closingEntry->entry_date)->format('Y/m/d') : '—' }}</td>
                                            <td class="fw-semibold">{{ $closingEntry->description }}</td>
                                            <td class="text-end fw-bold text-success">{{ number_format($closingEntry->total_debit, 2) }}</td>
                                            <td class="text-end fw-bold text-danger">{{ number_format($closingEntry->total_credit, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-light-primary">{{ $closingEntry->creator->name ?? '—' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('accusoft.JournalEntry.show', $closingEntry->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="عرض تفاصيل القيد">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <!--end::Closing Journal Entries Section-->

        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
