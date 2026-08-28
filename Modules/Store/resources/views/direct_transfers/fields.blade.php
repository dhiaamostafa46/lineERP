<div class="container-fluid py-4" id="direct-transfer-items-app">
    <!-- Card 1: Basic Information -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                @lang('store::models/st_direct_transfers.plural')
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_direct_transfers.fields.from_store_id') }}
                        <span class="text-danger">*</span>
                    </label>
                    <x-select2-input name="from_store_id" id="store_id" placeholder="المستودع المصدر" :list="$stores" :selected_id="old('from_store_id', @optional($transfer)->from_store_id ?? '')"
                        required>
                    </x-select2-input>
                    @error('from_store_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_direct_transfers.fields.to_store_id') }}
                        <span class="text-danger">*</span>
                    </label>
                    <x-select2-input name="to_store_id" id="to_store_id" placeholder="المستودع الهدف" :list="$stores" :selected_id="old('to_store_id', @optional($transfer)->to_store_id ?? '')"
                        required>
                    </x-select2-input>
                    @error('to_store_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_direct_transfers.fields.document_number') }}
                    </label>
                    {!! Form::hidden('transfer_type', old('transfer_type', @optional($transfer)->transfer_type ?? ($default_transfer_type ?? 1)), ['id' => 'transfer_type']) !!}
                    {!! Form::text(
                        'document_number',
                        isset($transfer)
                            ? $transfer->document_number
                            : $document_number,
                        [
                            'class' => 'form-control bg-light',
                            'readonly',
                        ],
                    ) !!}
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_direct_transfers.fields.document_date') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group" id="kt_td_picker_to" data-td-target-input="nearest" data-td-target-toggle="nearest">
                        <input id="kt_td_picker_to_input" type="text" name="document_date"
                            class="form-control"
                            data-td-target="#kt_td_picker_to"
                            value="{{ old('document_date', isset($transfer) ? optional($transfer)->document_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            required />
                        <span class="input-group-text" data-td-target="#kt_td_picker_to" data-td-toggle="datetimepicker">
                            <i class="ki-duotone ki-calendar fs-2">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_direct_transfers.fields.status') }}
                    </label>
                    @php
                        $canDraft = auth()->user()->can('store.direct_transfer.draft');
                        $canApprove = auth()->user()->can('store.direct_transfer.approve');
                        if ($canDraft && !$canApprove) {
                            $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k == \Modules\Store\App\Models\StDirectTransfer::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
                        } elseif ($canApprove && !$canDraft) {
                            $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k != \Modules\Store\App\Models\StDirectTransfer::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
                        } else {
                            $filteredStatuses = $statuses ?? [];
                        }
                    @endphp
                    <x-select2-input name="status" placeholder="الحالة" :list="$filteredStatuses" :selected_id="old(
                        'status',
                        @optional($transfer)->status ?? ($canDraft ? \Modules\Store\App\Models\StDirectTransfer::STATUS_DRAFT : 2),
                    )"
                        id="transfer-status">
                    </x-select2-input>
                </div>
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_direct_transfers.fields.notes') }}
                    </label>
                    {!! Form::textarea('notes', old('notes', @optional($transfer)->notes ?? ''), [
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => 'ملاحظات إضافية',
                    ]) !!}
                </div>
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold small text-muted">
                        <i class="ki-outline ki-paper-clip fs-5 me-1 text-primary"></i>
                        {{ __('lang.attachment') }}
                    </label>
                    <div class="card bg-light-secondary border border-dashed border-gray-400 p-3">
                        <input type="file" name="attachment" id="attachment" class="form-control bg-transparent border-0" accept="image/*,.pdf,.doc,.docx" />
                        @if(!empty($transfer?->attachment))
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <a href="{{ $transfer->attachment_url }}" target="_blank" class="badge badge-light-primary py-2 px-3">
                                    <i class="ki-outline ki-file fs-5 me-1 text-primary"></i> {{ $transfer->attachment }}
                                </a>
                            </div>
                        @endif
                    </div>
                    @error('attachment')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Items Details Component -->
    @include('store::components.items_details', [
        'document' => $transfer ?? null,
        'showBookQuantity' => true,
        'isSettlement' => false,
        'isTransferIn' => (isset($transfer) && ($transfer->status == 3 || $transfer->status == 5)),
    ])

    @push('scripts')
    <script>
        $(document).ready(function() {
            if (typeof tempusDominus !== 'undefined') {
                const pickerTo = document.getElementById('kt_td_picker_to');
                if (pickerTo) {
                    new tempusDominus.TempusDominus(pickerTo, {
                        display: {
                            components: {
                                calendar: true,
                                clock: false
                            },
                            buttons: {
                                today: true,
                                clear: true,
                                close: true
                            }
                        },
                        localization: {
                            format: 'yyyy-MM-dd'
                        }
                    });
                }
            }

            const allStatuses = @json($all_statuses);
            const statusSelect = $('#transfer-status');
            const fromStore = $('#store_id');
            const toStore = $('#to_store_id');

            function checkStores() {
                if (fromStore.val() && toStore.val() && fromStore.val() == toStore.val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'لا يمكن أن يكون المستودع المرسل هو نفسه المستقبِل',
                        confirmButtonText: 'حسناً'
                    });
                    toStore.val('').trigger('change');
                }
            }

            fromStore.on('change', checkStores);
            toStore.on('change', checkStores);

            $('#transfer_type').on('change', function() {
                const typeId = $(this).val();
                const currentStatus = statusSelect.val();
                
                statusSelect.empty();
                if (allStatuses[typeId]) {
                    $.each(allStatuses[typeId], function(id, text) {
                        const option = new Option(text, id, false, id == currentStatus);
                        statusSelect.append(option);
                    });
                }
                statusSelect.trigger('change');
            });
        });
    </script>
    @endpush
</div>
