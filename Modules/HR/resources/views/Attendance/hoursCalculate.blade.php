@extends('layouts.app')


@section('title', 'حاسبة خارج الدوام')

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
                       حساب ساعات خارج الدوام
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="" class="text-muted text-hover-primary">
                              حاسبة خارج الدوام
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            @lang('crud.create')
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="" class="btn btn-sm btn-secondary">
                        @lang('crud.cancel')
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
                @if (session('msg'))
                    @php
                        $msg = session('msg');
                    @endphp

                    @if (!$msg['status'])
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($msg['messages'] as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @else

                    @endif
                @endif
                <div class="card">



                    <div class="card-body">

                        <div class="row">

                         <!-- Employee Field -->
                              <div id="employee_field" class="form-group col-sm-6 mb-3">
                                {!! Form::label('employee_id', __('hr::models/hr_end_service.fields.employee') . ':') !!}
                                 <x-select2-input id="employeeSelect" name="employee_id" :placeholder="__('hr::lang.select_employee')" :list="$employees" :selected_id="old('employee_id', @optional($EndService)->employee_id ?? 0)">
                                    </x-select2-input>
                              </div>
                               <!-- Employee Field -->
                              <div id="employee_field" class="form-group col-sm-6 mb-3">
                                {!! Form::label('employee_id', __('عدد الساعات') . ':') !!}
                                 {!! Form::number('end', null, [ 'id'=>'counth','class' => 'form-control','placeholder' => __('أدخل عدد الساعات'),]) !!}
                              </div>

                               <div id="employee_field" class="form-group col-sm-6 mb-3">
                                {!! Form::label('employee_id', __('تكلفة الساعات خارج الدوام') . ':') !!}
                                 {!! Form::text('end', null, [ 'id'=>'resultamount','class' => 'form-control','readonly'=>true,]) !!}
                              </div>

                        </div>

                    </div>

                    <div class="card-footer py-4 text-end">
                        <a href="{{ route('hr.EndService.index') }}" class="btn btn-sm btn-secondary">

                            @lang('crud.cancel')
                        </a>
                            {!! Form::button('حساب ', ['class' => 'btn btn-sm btn-primary','id'=>'calc']) !!}

                    </div>



                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // تحميل بيانات الموظف عند تحميل الصفحة

        $('#employeeSelect').change(function() {


            //loadEmployeeData(employeeId);
        });

        function loadEmployeeData(employeeId)
        {
           var employeeId = $('#employeeSelect').val();
             $.ajax({
                    url: 'calculateHours/7/2',
                    type: 'get',
                    success: function(response) {

                        $('#resultamount').val(response.result)
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
        }



        $('#calc').click(function() {


            var employeeId = $('#employeeSelect').val();
            var hours = $('#counth').val();
             $.ajax({
                    url: 'calculateHours/'+employeeId+'/'+hours,
                    type: 'get',
                    success: function(response) {
                          $('#resultamount').val(response.result);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
        });

    });
  </script>
@endsection
