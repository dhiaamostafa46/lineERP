<div class="container-fluid py-4" id="damaged-items-app">
    <!-- Card 1: Basic Information -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
        <div class="card-header py-3 px-4 bg-transparent border-bottom">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                @lang('store::models/st_damageds.plural')
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_damageds.fields.store_id') }}
                        <span class="text-danger">*</span>
                    </label>
                    <x-select2-input name="store_id" id="store_id" :placeholder="__('store::models/st_damageds.fields.store_id')" :list="$stores" :selected_id="old('store_id', @optional($damaged)->store_id ?? '')"
                        required>
                    </x-select2-input>
                    @error('store_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_damageds.fields.document_number') }}
                    </label>
                    {!! Form::text(
                        'document_number',
                        isset($damaged)
                            ? $damaged->document_number
                            : \Modules\Store\App\Models\StDamaged::generateDocumentNumber(),
                        [
                            'class' => 'form-control bg-light',
                            'readonly',
                        ],
                    ) !!}
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_damageds.fields.document_date') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group" id="kt_td_picker_to" data-td-target-input="nearest" data-td-target-toggle="nearest">
                        <input id="kt_td_picker_to_input" type="text" name="document_date"
                            class="form-control"
                            data-td-target="#kt_td_picker_to"
                            value="{{ old('document_date', isset($damaged) ? optional($damaged)->document_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            required />
                        <span class="input-group-text" data-td-target="#kt_td_picker_to" data-td-toggle="datetimepicker">
                            <i class="ki-duotone ki-calendar fs-2">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </span>
                    </div>
                    @error('document_date')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_damageds.fields.status') }}
                    </label>
                    @php
                        $canDraft = auth()->user()->can('store.damaged.draft');
                        $canApprove = auth()->user()->can('store.damaged.approve');
                        if ($canDraft && !$canApprove) {
                            $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k == \Modules\Store\App\Models\StDamaged::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
                        } elseif ($canApprove && !$canDraft) {
                            $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k != \Modules\Store\App\Models\StDamaged::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
                        } else {
                            $filteredStatuses = $statuses ?? [];
                        }
                    @endphp
                    <x-select2-input name="status" :placeholder="__('store::models/st_damageds.fields.status')" :list="$filteredStatuses" :selected_id="old(
                        'status',
                        @optional($damaged)->status ?? ($canDraft ? \Modules\Store\App\Models\StDamaged::STATUS_DRAFT : 2),
                    )"
                        id="damaged-status">
                    </x-select2-input>
                    @error('status')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold small text-muted">
                        {{ __('store::models/st_damageds.fields.notes') }}
                    </label>
                    {!! Form::textarea('notes', old('notes', @optional($damaged)->notes ?? ''), [
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => __('store::models/st_damageds.fields.notes'),
                    ]) !!}
                    @error('notes')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6 mt-4">
                    <label class="form-label fw-bold small text-muted">
                        <i class="ki-outline ki-paper-clip fs-5 me-1 text-primary"></i>
                        {{ __('lang.attachment') }}
                    </label>
                    <div class="card bg-light-secondary border border-dashed border-gray-400 p-3">
                        <input type="file" name="attachment" id="attachment" class="form-control bg-transparent border-0" accept="image/*,.pdf,.doc,.docx" />
                        @if(!empty($damaged?->attachment))
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <a href="{{ $damaged->attachment_url }}" target="_blank" class="badge badge-light-primary py-2 px-3">
                                    <i class="ki-outline ki-file fs-5 me-1 text-primary"></i> {{ $damaged->attachment }}
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
        'document' => $damaged ?? null,
        'showBookQuantity' => true,
        'isSettlement' => false,
    ])
</div>

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
        });
    </script>
@endpush
