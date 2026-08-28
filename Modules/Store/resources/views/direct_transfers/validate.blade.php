@extends('layouts.app')

@section('title', __('store::models/st_direct_transfers.status.completed'))

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ __('store::models/st_direct_transfers.status.completed') }}: {{ $transfer->document_number }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">@lang('lang.dashboard')</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('store.direct_transfer.index') }}"
                                class="text-muted text-hover-primary">{{ __('store::models/st_direct_transfers.plural') }}</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            {{ __('store::models/st_direct_transfers.status.completed') }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('store.direct_transfer.index') }}"
                        class="btn btn-sm btn-secondary">
                        <i class="bi bi-x-lg me-1"></i> @lang('crud.cancel')
                    </a>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                @include('adminlte-templates::common.errors')

                {!! Form::model($transfer, [
                    'route' => ['store.direct_transfer.store_validation', $transfer->id],
                    'method' => 'POST',
                ]) !!}

                <!-- Basic Information Card -->
                <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
                    <div class="card-header py-3 px-4 bg-transparent border-bottom">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            {{ __('store::ui.transfer_details') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label
                                    class="form-label fw-bold small text-muted">{{ __('store::models/st_direct_transfers.fields.from_store_id') }}</label>
                                <div class="form-control bg-light border-0 py-3">{{ $transfer->fromStore->name }}</div>
                                <input type="hidden" name="from_store_id" value="{{ $transfer->from_store_id }}">
                            </div>
                            <div class="col-md-3">
                                <label
                                    class="form-label fw-bold small text-muted">{{ __('store::models/st_direct_transfers.fields.to_store_id') }}</label>
                                <div class="form-control bg-light border-0 py-3">{{ $transfer->toStore->name }}</div>
                                <input type="hidden" name="to_store_id" value="{{ $transfer->to_store_id }}">
                            </div>
                            <div class="col-md-2">
                                <label
                                    class="form-label fw-bold small text-muted">{{ __('store::models/st_direct_transfers.fields.document_number') }}</label>
                                <div class="form-control bg-light border-0 py-3">{{ $transfer->document_number }}</div>
                                <input type="hidden" name="document_number" value="{{ $transfer->document_number }}">
                            </div>
                            <div class="col-md-2">
                                <label
                                    class="form-label fw-bold small text-muted">{{ __('store::models/st_direct_transfers.fields.document_date') }}</label>
                                <div class="form-control bg-light border-0 py-3">{{ $transfer->document_date->format('Y-m-d') }}</div>
                                <input type="hidden" name="document_date" value="{{ $transfer->document_date->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-muted">{{ __('store::ui.current_status') }}</label>
                                <div class="badge badge-light-primary fw-bold fs-7 p-3 w-100 text-center">{{ $transfer->status_text }}</div>
                            </div>
                        </div>
                        <div class="row g-4 mt-1">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">{{ __('store::ui.return_status') }}</label>
                                <div class="badge badge-light-{{ $transfer->return_status == 3 ? 'success' : ($transfer->return_status == 1 ? 'warning' : 'secondary') }} fw-bold fs-7 p-3 w-100 text-center">
                                    {{ $transfer->return_status == 3 ? __('store::ui.full_return') : ($transfer->return_status == 1 ? __('store::ui.partial_return') : __('store::ui.no_return')) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">{{ __('store::ui.total_returned_qty') }}</label>
                                <div class="form-control bg-light-danger border-0 py-3 text-danger fw-bold">{{ number_format($transfer->returned_quantity, 2) }}</div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">
                                    {{ __('store::models/st_direct_transfers.fields.notes') }}
                                </label>
                                {!! Form::textarea('notes', old('notes', $transfer->notes ?? ''), [
                                    'class' => 'form-control',
                                    'rows' => 2,
                                    'placeholder' => __('store::ui.receive_notes_placeholder'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Details Component -->
                <div class="mb-4">
                    @include('store::components.items_details', [
                        'document' => $transfer,
                        'showBookQuantity' => true,
                        'isSettlement' => false,
                        'isTransferIn' => true,
                    ])
                </div>

                <!-- Action Buttons Card -->
                <div class="card border-0 rounded-3 shadow-sm mb-5 bg-white">
                    <div class="card-footer py-5 px-4 bg-transparent d-flex justify-content-end align-items-center gap-3">
                        @php
                            $inTransitCount = 0;
                            foreach ($transfer->items as $item) {
                                $inTransitCount += (float) $item->quantity - (float) $item->received_quantity;
                            }

                            $isFinalized = in_array($transfer->status, [
                                \Modules\Store\App\Models\StDirectTransfer::STATUS_DESTINATION_APPROVED,
                                \Modules\Store\App\Models\StDirectTransfer::STATUS_CANCELLED,
                                \Modules\Store\App\Models\StDirectTransfer::STATUS_RETURNED,
                            ]);
                        @endphp

                        @if (!$isFinalized)
                            <input type="hidden" name="status" id="form-status-input" value="{{ $transfer->status }}">

                            {{-- Button 3: Destination Draft --}}
                            <button type="submit" onclick="document.getElementById('form-status-input').value='3'"
                                class="btn btn-light-info shadow-sm px-6">
                                <i class="bi bi-pencil-square fs-5 me-2"></i> {{ __('crud.save_draft') }}
                            </button>



                            {{-- Button 5: Approve Receipt --}}
                            <button type="submit" onclick="document.getElementById('form-status-input').value='5'"
                                class="btn btn-primary shadow-sm px-10">
                                <i class="bi bi-check2-circle fs-4 me-2"></i> {{ __('store::ui.confirm_receipt') }}
                            </button>
                        @else
                            <div class="alert alert-light-secondary d-flex align-items-center p-4 mb-0 border-dashed w-100 rounded-3">
                                <i class="bi bi-info-circle-fill fs-3 text-secondary me-3"></i>
                                <span class="fw-bold text-gray-700 fs-6">{{ __('store::messages.or_cannot_edit') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
