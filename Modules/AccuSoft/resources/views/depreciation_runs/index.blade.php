@extends('layouts.app')

@section('title', 'دورات الإهلاك')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    إدارة دورات الإهلاك المجمعة
                </h1>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('accusoft.depreciation_runs.create') }}" class="btn btn-sm fw-bold btn-primary">
                    تشغيل دورة إهلاك جديدة
                </a>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @include('flash::message')
            
            <div class="card card-flush">
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-striped text-center gy-7 gs-7 mt-5">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">تاريخ الدورة</th>
                                    <th class="text-center">شهر/سنة الإهلاك</th>
                                    <th class="text-center">إجمالي الإهلاك</th>
                                    <th class="text-center">رقم القيد</th>
                                    <th class="text-center">الحالة</th>
                                    <th class="text-center">بواسطة</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @forelse($runs as $run)
                                    <tr>
                                        <td class="text-center">{{ $run->run_date }}</td>
                                        <td class="text-center">{{ $run->run_month }} / {{ $run->run_year }}</td>
                                        <td class="text-center">{{ number_format($run->total_depreciation, 2) }}</td>
                                        <td class="text-center">
                                            @if($run->journal_entry_id)
                                                <a href="{{ route('accusoft.JournalEntry.show', $run->journal_entry_id) }}">
                                                    عرض القيد
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light-success">مكتمل</span>
                                        </td>
                                        <td class="text-center">{{ $run->creator->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">لا يوجد دورات إهلاك سابقة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4 mt-4">
                        <div class="float-right">
                            @include('adminlte-templates::common.paginate', ['records' => $runs])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
