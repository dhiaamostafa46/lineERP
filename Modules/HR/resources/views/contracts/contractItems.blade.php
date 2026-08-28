@extends('layouts.app')

@section('title', __('hr::models/hr_contract_items.plural'))



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
                    <h1>@lang('hr::models/hr_contract_items.plural')</h1>
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('dashboard') }}" class=" text-muted text-hover-primary">
                            @lang('lang.dashboard')
                        </a>
                    </li>
                    <!--end::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('hr.contracts.index') }}" class=" text-muted text-hover-primary">
                            @lang('hr::models/hr_contracts.plural')
                        </a>
                    </li>
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        @lang('hr::models/hr_contract_items.plural')
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class=" btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#kt_modal_1">
                    {{ trans('hr::models/hr_contract_items.singular') }}
                </button>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            <div class="clearfix"></div>

            <div class="card">


                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped gy-7 gs-7" id="hr-contracts-table">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200 ">
                                    <th>@lang('hr::models/hr_contract_items.fields.description_ar')</th>
                                    <th>@lang('hr::models/hr_contract_items.fields.description_en')</th>


                                    <th colspan="3" class="text-center">@lang('crud.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contract->Contractitem as $index=>$contract)
                                <tr>


                                    <td>{{ $contract->Desc_ar }}</td>
                                    <td>{{ $contract->Desc_En }}</td>



                                    <div class="modal fade" tabindex="-1" id="kt_modal_1_edite{{ $index}}">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                {!! Form::model($contract, [
                                                    'route' => ['hr.ContractItem.update', $contract->id],
                                                    'method' => 'patch',
                                                    'files' => true,
                                                    ]) !!}
                                                <div class="modal-header">
                                                    <h3 class="modal-title">{{ trans('hr::models/hr_contract_items.singular') }} </h3>

                                                    <!--begin::Close-->
                                                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                    <!--end::Close-->
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="employee_id" value="{{ $contract->employee_id }}">
                                                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                                                    <!-- Arabic Description Field -->
                                                    <div class="form-group col-sm-12 mb-3">
                                                        {!! Form::label('Desc_ar', __('hr::models/hr_contract_items.fields.description_ar') . ':') !!}
                                                        {!! Form::textarea('Desc_ar',  @optional($contract)->Desc_ar, ['class' => 'form-control']) !!}
                                                    </div>

                                                    <!-- English Description Field -->
                                                    <div class="form-group col-sm-12 mb-3">
                                                        {!! Form::label('Desc_En', __('hr::models/hr_contract_items.fields.description_en') . ':') !!}
                                                        {!! Form::textarea('Desc_En',  @optional($contract)->Desc_En, ['class' => 'form-control']) !!}
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <a data-bs-dismiss="modal" class="btn btn-sm btn-secondary">
                                                        @lang('crud.cancel')
                                                    </a>
                                                    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-sm btn-primary']) !!}
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>



                                    <td><span class="{{ $contract->status_badge }}">{{ $contract->status_text }}</span></td>
                                    <td style="width: 120px">
                                        {!! Form::open(['route' => ['hr.ContractItem.destroy', $contract->id], 'method' => 'delete']) !!}
                                        <div class='btn-group'>


                                            <a data-bs-toggle="modal" data-bs-target="#kt_modal_1_edite{{ $index}}"
                                                class='btn btn-icon btn-sm btn-light-primary btn-xs'>
                                                <i class="fa-solid fa-edit"></i>
                                            </a>


                                            @can('hr.contracts.destroy')
                                            {!! Form::button('<i class="fa-solid fa-trash"></i>', [
                                            'type' => 'submit',
                                            'class' => 'btn btn-icon btn-sm btn-light-danger btn-xs',
                                            'onclick' => "return confirm('Are you sure?')",
                                            ]) !!}
                                            @endcan
                                        </div>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix py-4">
                        <div class="float-right">

                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>
    <!--end::Content-->
</div>


<div class="modal fade" tabindex="-1" id="kt_modal_1">
    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'hr.ContractItem.store', 'files' => true]) !!}
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('hr::models/hr_contract_items.singular') }} </h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <input type="hidden" name="employee_id" value="{{ $contract->employee_id }}">
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <!-- Arabic Description Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('Desc_ar', __('hr::models/hr_contract_items.fields.description_ar') . ':') !!}
                    {!! Form::textarea('Desc_ar', null, ['class' => 'form-control']) !!}
                </div>

                <!-- English Description Field -->
                <div class="form-group col-sm-12 mb-3">
                    {!! Form::label('Desc_En', __('hr::models/hr_contract_items.fields.description_en') . ':') !!}
                    {!! Form::textarea('Desc_En', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="modal-footer">
                <a data-bs-dismiss="modal" class="btn btn-sm btn-secondary">
                    @lang('crud.cancel')
                </a>
                {!! Form::submit(__('crud.save'), ['class' => 'btn btn-sm btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection




