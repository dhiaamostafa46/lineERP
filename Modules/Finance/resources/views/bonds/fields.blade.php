<div class="row g-9">
    <!-- Header Section -->

    <!-- Main Form Fields -->
    <div class="col-md-4">
        <label class="form-label fw-bold @isset($bond) text-muted @endisset">
            <i class="ki-outline ki-category fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.bond_type') }}
            <span class="text-danger">*</span>
        </label>
        <div class="d-flex align-items-center gap-10 mt-3">
            @foreach($types as $value => $label)
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="radio"
                           name="bond_type"
                           value="{{ $value }}"
                           id="bond_type_{{ $value }}"
                           {{ (string)old('bond_type', $bond->bond_type ?? ($loop->first ? $value : '')) === (string)$value ? 'checked' : '' }}
                           {{ isset($bond) ? 'disabled' : '' }} required />
                    <label class="form-check-label fw-bold text-gray-800" for="bond_type_{{ $value }}">
                        {{ $label }}
                    </label>
                </div>
            @endforeach
        </div>
        @if(isset($bond))
            <input type="hidden" name="bond_type" value="{{ $bond->bond_type }}">
        @endif
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-calendar fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.date') }}
            <span class="text-danger">*</span>
        </label>
        <div class="input-group" id="kt_td_picker_date" data-td-target-input="nearest" data-td-target-toggle="nearest">
            <input id="kt_td_picker_date_input" type="text" name="date" class="form-control"
                   data-td-target="#kt_td_picker_date_input"
                   value="{{ old('date', isset($bond) && $bond->date ? $bond->date->format('Y-m-d') : date('Y-m-d')) }}" required/>
            <span class="input-group-text" data-td-target="#kt_td_picker_date_input" data-td-toggle="datetimepicker">
                <i class="ki-outline ki-calendar fs-2"></i>
            </span>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-barcode fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.reference_number') }}
        </label>
        {!! Form::text('reference_number', null, ['class' => 'form-control', 'placeholder' => __('finance::models/fnc_bond.fields.reference_number')]) !!}
    </div>

    <!-- Account Details Section -->
    <div class="col-12 mt-5">
        <div class="separator separator-dashed border-gray-300"></div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-bank fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.fund_account_id') }}
            <span class="text-danger">*</span>
        </label>
        <x-select2-input name="fund_account_id" :placeholder="__('lang.select')" :list="$fundAccounts" :selected_id="old('fund_account_id', $bond->fund_account_id ?? null)" required></x-select2-input>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-user fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.contact_account_id') }}
            <span class="text-danger">*</span>
        </label>
        <x-select2-input name="contact_account_id" :placeholder="__('lang.select')" :list="$contactAccounts" :selected_id="old('contact_account_id', $bond->contact_account_id ?? null)" required></x-select2-input>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-price-tag fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.amount') }}
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light-primary border-primary">
                <i class="ki-outline ki-wallet fs-2 text-primary"></i>
            </span>
            {!! Form::number('amount', null, ['class' => 'form-control border-primary fw-bold text-primary', 'step' => '0.01', 'min' => '0.01', 'required']) !!}
        </div>
    </div>

    <!-- Logistics Section -->
    <div class="col-12 mt-5">
        <div class="separator separator-dashed border-gray-300"></div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-graph-up fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.cost_center_id') }}
        </label>
        <x-select2-input name="cost_center_id" :placeholder="__('lang.select')" :list="$costCenters" :selected_id="old('cost_center_id', $bond->cost_center_id ?? null)"></x-select2-input>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-geolocation fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.branch_id') }}
        </label>
        <x-select2-input name="branch_id" :placeholder="__('lang.select')" :list="$branches" :selected_id="old('branch_id', $bond->branch_id ?? null)"></x-select2-input>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-status fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.status') }}
            <span class="text-danger">*</span>
        </label>
        @php
            $canDraft = auth()->user()->can('fnc.bonds.draft');
            $canApprove = auth()->user()->can('fnc.bonds.approve');
            if ($canDraft && !$canApprove) {
                $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k == \Modules\Finance\App\Models\FncBond::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
            } elseif ($canApprove && !$canDraft) {
                $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k == \Modules\Finance\App\Models\FncBond::STATUS_APPROVED, ARRAY_FILTER_USE_KEY);
            } else {
                $filteredStatuses = $statuses ?? [];
            }
        @endphp
        <x-select2-input name="status" :placeholder="__('lang.select')" :list="$filteredStatuses" :selected_id="old('status', $bond->status ?? ($canDraft ? \Modules\Finance\App\Models\FncBond::STATUS_DRAFT : \Modules\Finance\App\Models\FncBond::STATUS_APPROVED))" required></x-select2-input>
    </div>

    <!-- Full Width Fields -->
    <div class="col-md-6 mt-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-paper-clip fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.attachment') }}
        </label>
        <div class="card bg-light-secondary border border-dashed border-gray-400 p-5">
            <input type="file" name="attachment" id="attachment" class="form-control bg-transparent border-0" accept="image/*,.pdf,.doc,.docx" />
        </div>
    </div>

    <div class="col-md-6 mt-4">
        <label class="form-label fw-bold">
            <i class="ki-outline ki-message-text-2 fs-3 me-1 text-primary"></i>
            {{ __('finance::models/fnc_bond.fields.description') }}
        </label>
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('lang.enter_description')]) !!}
    </div>
</div>



@push('scripts')
  <script>
            new tempusDominus.TempusDominus(document.getElementById('kt_td_picker_date_input'), {
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
                } // لتوافق قاعدة البيانات
            });


        </script>
@endpush
