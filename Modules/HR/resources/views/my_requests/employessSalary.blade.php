@extends('layouts.app')

@section('title', __('hr::lang.my_requests'))

@section('content')


<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printDiv, #printDiv * {
            visibility: visible;
        }
        #printDiv {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
    }
</style>

    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                      تعريف بالراتب
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}"
                                class=" text-muted
                            text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('hr.empdashboard.index') }}" class=" text-muted text-hover-primary">
                                @lang('hr::lang.my_requests')
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->

                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    {{-- <a href="{{ route('hr.jobs.index') }}" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a> --}}
                <a class="btn btn-sm btn-primary float-right" onclick="printDiv()">
                    <i class="fa-solid fa fa-print"></i>
                    @lang('crud.print')
                </a>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                @include('adminlte-templates::common.errors')
                <div class="clearfix"></div>
                <div class="card shadow-lg border-0" id="printDiv">
                    <div class="card-body px-5 py-4">
                        <div class="container">
                            <!-- عنوان الخطاب -->
                            <div class="text-center mb-5">
                                <h2 class="font-weight-bold text-primary">شهادة تعريف</h2>
                                <hr class="w-25 mx-auto" style="border-top: 2px solid #007bff;">
                            </div>

                            <!-- بيانات المنشأة -->
                            <h4 class="mb-4 text-secondary">بيانات المنشأة:</h4>
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold bg-light" style="width: 30%;">اسم الشركة:</td>
                                        <td>{{$Org->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">رقم السجل التجاري:</td>
                                        <td>{{$Org->tax_number}}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <hr class="my-5">
                                 {{-- @dd($employee) --}}
                            <!-- نص الشهادة -->
                            <p class="my-4 text-justify" style="line-height: 1.8; font-size: 1.1rem;">
                                <strong>إلى من يهمه الأمر،</strong><br>
                                تشهد شركة <strong>{{$Org->name}}</strong><br>
                                أن الموظف <strong>{{$employee->main_employee->full_name ?? ''}}</strong>،
                                هوية رقم <strong>{{$employee->main_employee->identity->identity_no ?? ''}}</strong>،
                                يعمل لدينا بمسمى وظيفي: <strong>{{$employee->job->name ?? ''}}</strong>، منذ تاريخ <strong>{{$employee->start_at}}</strong>،
                                ولا يزال على رأس العمل براتب شهري وقدره <strong>{{number_format($employee->salary->basic + $employee->salary->totalAllowance(), 2)}}</strong> ريال.
                                <br>
                                تم إصدار هذه الشهادة بناءً على طلبه دون أدنى مسؤولية على الشركة.
                            </p>

                            <!-- التاريخ -->
                            <div class="d-flex justify-content-end">
                                <p class="font-weight-bold">
                                    <strong>التاريخ:</strong> {{date("Y-m-d")}}
                                </p>
                            </div>

                            <!-- التوقيع والملاحظات -->
                            <div class="footer text-center py-5">
                                <p class="text-muted" style="font-size: 0.95rem;">
                                    <em>ملاحظة هامة:</em><br>
                                    صلاحية هذا الخطاب تنتهي بتاريخ:
                                    <strong class="text-danger">{{date("Y-m-d", strtotime('+30 days'))}}</strong>
                                </p>
                                <p class="mt-5 font-weight-bold">
                                    توقيع المسؤول:
                                </p>
                                <div class="mt-3" style="width: 50%; margin: 0 auto; ">
                                    <img src="{{ $Org->seal_original_path }}" alt="ختم الشركة" style="max-width: 150px; max-height: 150px;">
                                   {{-- src="{{ asset('admin_assets') }}/media/logos/Logoevix.png" --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
















    <script>
        function printDiv() {
            const printContents = document.getElementById("printDiv").innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            // إعادة تحميل الصفحة لإعادة المحتوى الأصلي
            window.location.reload();
        }
    </script>








@endsection
