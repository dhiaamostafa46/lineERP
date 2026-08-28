<!-- Dispose Modal -->
<div class="modal fade" id="disposeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-1"></i>
                </div>
            </div>
            
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form action="{{ route('accusoft.assets.dispose', $asset->id) }}" method="POST" class="form" id="kt_modal_dispose_form">
                    @csrf
                    
                    <div class="mb-13 text-center">
                        <h1 class="mb-3 text-danger">@lang('accusoft::messages.dispose_asset_title')</h1>
                        <div class="text-muted fw-semibold fs-5">
                            @lang('accusoft::messages.dispose_asset_subtitle', ['name' => $asset->name])
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.fields.disposal_date')</label>
                            <input type="date" name="disposal_date" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.fields.disposal_type')</label>
                            <select name="disposal_type" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                @foreach(\Modules\AccuSoft\App\Models\Asset::getDisposalTypes() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.fields.disposal_value')</label>
                        <div class="position-relative">
                            <input type="number" name="disposal_value" class="form-control form-control-solid" step="0.01" value="0" required>
                            <div class="position-absolute translate-middle-y top-50 end-0 me-3 text-muted">@lang('lang.local_currency')</div>
                        </div>
                    </div>

                    <div class="row g-9 mb-8">
                        <div class="col-md-12 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.fields.cash_account_id')</label>
                            <select name="cash_account_id" class="form-select form-select-solid account-select" required></select>
                        </div>
                    </div>

                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                        <i class="fa-solid fa-triangle-exclamation fs-2tx text-warning me-4"></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">@lang('accusoft::messages.accounting_warning')</h4>
                                <div class="fs-6 text-gray-700">@lang('accusoft::messages.dispose_warning_message')</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">@lang('crud.cancel')</button>
                        <button type="submit" class="btn btn-danger">
                            <span class="indicator-label">@lang('accusoft::messages.confirm_dispose')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Depreciate Modal -->
<div class="modal fade" id="depreciateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark fs-1"></i>
                </div>
            </div>
            
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form action="{{ route('accusoft.assets.depreciate', $asset->id) }}" method="POST" class="form">
                    @csrf
                    
                    <div class="mb-13 text-center">
                        <h1 class="mb-3 text-success">@lang('accusoft::messages.manual_depreciation_title')</h1>
                        <div class="text-muted fw-semibold fs-5">
                            @lang('accusoft::messages.manual_depreciation_subtitle', ['name' => $asset->name])
                        </div>
                    </div>

                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.fields.depreciation_date')</label>
                        <input type="date" name="depreciation_date" class="form-control form-control-solid" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required>
                    </div>

                    @if($asset->calculation_type === 'manual')
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.manual_depreciation_amount')</label>
                        <input type="number" name="amount" class="form-control form-control-solid" step="0.01" min="0" placeholder="@lang('accusoft::models/as_asset.leave_blank_for_auto')">
                        <div class="text-muted fs-7 mt-2">@lang('accusoft::models/as_asset.fields.current_book_value'): {{ number_format($asset->current_book_value ?? $asset->purchase_value, 2) }}</div>
                    </div>
                    
                    <div class="fv-row mb-8">
                        <label class="fs-6 fw-semibold mb-2">@lang('accusoft::models/as_asset.notes_optional')</label>
                        <textarea name="notes" class="form-control form-control-solid" rows="3"></textarea>
                    </div>
                    @endif

                    <div class="text-center">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-light me-3">@lang('crud.cancel')</button>
                        <button type="submit" class="btn btn-success">
                            <span class="indicator-label">@lang('accusoft::messages.record_depreciation')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
