@extends('layouts.app')

@section('title', __('hr::models/hr_justifications.singular'))

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
                        @lang('crud.create') @lang('hr::models/hr_justifications.singular')
                    </h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('hr.empdashboard.index') }}"
                                class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('hr.empdashboard.index') }}" class="text-muted text-hover-primary">
                                @lang('hr::models/hr_justifications.plural')
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
                    <a href="{{ route('hr.empdashboard.index') }}" class="btn btn-sm btn-secondary">
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
                <div class="card">

                    {!! Form::open(['route' => 'hr.justifications.store', 'files' => true]) !!}

                    <div class="card-body">

                        <div class="row">
                            {!! Form::hidden('employee_id', $employee->id) !!}
                            <div class="form-group col-sm-6 mb-3">
                                {!! Form::label('shift_id', __('hr::models/hr_justifications.fields.shift_id') . ':') !!}
                                <x-select2-input id="shift_id" name="shift_id" :list="$shifts" :placeholder="__('hr::lang.select_shift')" />
                            </div>

                            <div class="form-group col-sm-6 mb-3">
                                {!! Form::label('type', __('hr::models/hr_justifications.fields.type') . ':') !!}
                                <x-select2-input id="type_justification" name="type" :list="$types" :placeholder="__('hr::lang.select_type')" />
                            </div>

                            <!-- Attachment Field -->


                            <!-- Request Date Field -->
                            <div class="form-group col-sm-6 mb-3">
                                {!! Form::label('request_date', __('hr::models/hr_justifications.fields.request_date') . ':') !!}
                                {!! Form::date('request_date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            </div>
                             <div class="form-group col-sm-6 col-lg-6 mb-3">
                                {!! Form::label('attachment', __('hr::models/hr_justifications.fields.attachment') . ':') !!}
                                {!! Form::file('attachment', ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group from_to_container col-sm-6 mb-3" style="display: none">
                                {!! Form::label('from_time', __('hr::models/hr_justifications.fields.from') . ':') !!}
                                {!! Form::time('from_time', null, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group from_to_container col-sm-6 mb-3" style="display: none">
                                {!! Form::label('to_time', __('hr::models/hr_justifications.fields.to') . ':') !!}
                                {!! Form::time('to_time', null, ['class' => 'form-control']) !!}
                            </div>

                            <!-- Type Field -->




                            <!-- Reason Field -->
                            <div class="form-group col-sm-12 col-lg-12 mb-3">
                                {!! Form::label('reason', __('hr::models/hr_justifications.fields.reason') . ':') !!}
                                {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>



                        </div>

                    </div>

                    <div class="card-footer py-4 text-end">
                        <a href="{{ route('hr.empdashboard.index') }}" class="btn btn-sm btn-secondary">
                            @lang('crud.cancel')
                        </a>
                        {!! Form::submit('Save', ['class' => 'btn btn-sm btn-primary']) !!}
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {

                const type_justification = $('#type_justification');




                const fromToContainer = $('.from_to_container');

                type_justification.on('change', function() {


                    if ($(this).val() == '4') {
                        fromToContainer.show();
                    } else {
                        fromToContainer.hide();
                    }
                });



            });
        </script>
    @endpush

@endsection
