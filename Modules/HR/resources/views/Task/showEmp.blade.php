@extends('layouts.app')

@section('title', __('hr::models/hr_tasks.singular'))

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
                    @lang('hr::models/hr_tasks.singular') @lang('crud.detail')
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class=" text-muted
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
                        <a href="{{ route('hr.Task.index') }}" class=" text-muted text-hover-primary">
                            @lang('hr::models/hr_tasks.plural')
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
                        @lang('crud.back')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
              <div class="d-flex align-items-center gap-2 gap-lg-3">
                 <a class="btn btn-sm btn-secondary float-right"  data-bs-toggle="modal" data-bs-target="#Create_task_details">
                     @lang('lang.add_reply')
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
            <div class="card">
                <div class="card-body">
                    <div class="row gap-1">
                        @include('hr::Task.card')
                        {!! Form::model($Tasts, [
                            'route' => ['hr.Task.update', $Tasts->id],
                            'method' => 'patch',
                            'files' => true,
                            ]) !!}
                        <!-- Status Field -->
                         <div class="form-group col-sm-6 mb-3">
                          {!! Form::label('status', __('hr::models/hr_tasks.fields.status') . ':') !!}
                           <x-select2-input name="status" :placeholder="__('hr::lang.select_status')" :list="$statuses" :selected_id="old('status', @optional($task)->status ?? 0)">
                           </x-select2-input>
                         </div>
                         <div class="card-footer py-4 text-end">
                            <a href="{{ route('hr.Task.index') }}" class="btn btn-sm btn-secondary"> @lang('crud.cancel')
                            </a>
                            {!! Form::submit('Save', ['class' => 'btn btn-sm btn-primary']) !!}
                        </div>
                        <input type="hidden" value="{{$Tasts->title}}" name="title">
                        <input type="hidden" value="{{$Tasts->description}}" name="description">
                 
                         {!! Form::close() !!}

                    </div>
                </div>
            </div>
          
           

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</div>
@endsection
