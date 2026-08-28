{{-- Journal Entry Form - Professional Design --}}

{{-- ══════════════════════════════════════════════════════════════
     1. HEADER SECTION — Entry Info
══════════════════════════════════════════════════════════════ --}}
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
    <div class="card-header py-3 px-4 bg-transparent border-bottom">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            @lang('accusoft::models/as_journal_entries.messages.basic_info')
        </h5>
    </div>
    <div class="card-body p-4">
        {!! Form::hidden('lock_password', request('lock_password')) !!}

        <div class="row g-4">

            {{-- Hidden Entry Number & Source --}}
            {!! Form::hidden('entry_number', $journalEntry->entry_number ?? ($entryNumber ?? null), ['id' => 'entry_number_field']) !!}
            {!! Form::hidden('source', old('source', @optional($journalEntry)->source ?? \App\Models\AccuSoft\JournalEntry::SOURCE_MANUAL)) !!}

            {{-- Entry Date --}}
            <div class="col-md-3">
                {!! Form::label('entry_date', __('accusoft::models/as_journal_entries.fields.entry_date') . ': *') !!}
                <div class="input-group" id="kt_td_picker_entry_date"
                     data-td-target-input="nearest" data-td-target-toggle="nearest">
                    {!! Form::text(
                        'entry_date',
                        old('entry_date', isset($journalEntry)
                            ? optional($journalEntry->entry_date)->format('Y-m-d')
                            : now()->format('Y-m-d')
                        ),
                        [
                            'class'          => 'form-control form-control-solid',
                            'id'             => 'entry_date',
                            'data-td-target' => '#kt_td_picker_entry_date',
                            'required',
                        ]
                    ) !!}
                    <span class="input-group-text" data-td-target="#kt_td_picker_entry_date" data-td-toggle="datetimepicker">
                        <i class="ki-duotone ki-calendar fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </div>
            </div>

            {{-- Entry Type --}}
            <div class="col-md-2">
                {!! Form::label('entry_type', __('accusoft::models/as_journal_entries.fields.entry_type') . ': *') !!}
                <x-select2-input
                    name="entry_type"
                    :placeholder="__('accusoft::models/as_journal_entries.fields.entry_type')"
                    :list="$types ?? []"
                    :selected_id="old('entry_type', @optional($journalEntry)->entry_type ?? \App\Models\AccuSoft\JournalEntry::ENTRY_TYPE_MANUAL)">
                </x-select2-input>
            </div>

            {{-- Status --}}
            <div class="col-md-2">
                {!! Form::label('status', __('accusoft::models/as_journal_entries.fields.status') . ':') !!}
                @php
                    $canDraft = auth()->user()->can('accusoft.JournalEntry.draft');
                    $canApprove = auth()->user()->can('accusoft.JournalEntry.approve');
                    if ($canDraft && !$canApprove) {
                        $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k == \App\Models\AccuSoft\JournalEntry::STATUS_DRAFT, ARRAY_FILTER_USE_KEY);
                    } elseif ($canApprove && !$canDraft) {
                        $filteredStatuses = array_filter($statuses ?? [], fn($k) => $k == \App\Models\AccuSoft\JournalEntry::STATUS_POSTED, ARRAY_FILTER_USE_KEY);
                    } else {
                        $filteredStatuses = $statuses ?? [];
                    }
                @endphp
                <x-select2-input
                    name="status"
                    :placeholder="__('accusoft::models/as_journal_entries.fields.status')"
                    :list="$filteredStatuses"
                    :selected_id="old('status', @optional($journalEntry)->status ?? ($canDraft ? \App\Models\AccuSoft\JournalEntry::STATUS_DRAFT : \App\Models\AccuSoft\JournalEntry::STATUS_POSTED))">
                </x-select2-input>
            </div>

            {{-- Branch --}}
            @can('global.viewBranches')
                <div class="col-md-2">
                    {!! Form::label('branch_id', __('accusoft::models/as_journal_entries.fields.branch_id') . ':') !!}
                    <x-select2-input
                        name="branch_id"
                        :placeholder="__('accusoft::models/as_journal_entries.fields.branch_id')"
                        :list="$branchs ?? []"
                        :selected_id="old('branch_id', isset($journalEntry) ? $journalEntry->branch_id : auth()->user()->branch_id)"
                        required>
                    </x-select2-input>
                </div>
            @else
                {!! Form::hidden('branch_id', old('branch_id', isset($journalEntry) ? $journalEntry->branch_id : auth()->user()->branch_id)) !!}
            @endcan

            {{-- Attachment --}}
            <div class="col-md-3">
                {!! Form::label('attachment', __('accusoft::models/as_journal_entries.fields.attachment') . ':') !!}
                {!! Form::file('attachment', ['class' => 'form-control']) !!}
                @if (!empty($journalEntry?->attachment))
                    <a href="{{ Storage::url($journalEntry->attachment) }}" target="_blank" class="je-attachment-link">
                        <i class="fas fa-paperclip"></i> @lang('crud.download')
                    </a>
                @endif
            </div>

            {{-- Description --}}
            <div class="col-12">
                {!! Form::label('description', __('accusoft::models/as_journal_entries.fields.description') . ':') !!}
                {!! Form::textarea('description', old('description', $journalEntry->description ?? null), [
                    'class'       => 'form-control',
                    'rows'        => 2,
                    'placeholder' => __('accusoft::models/as_journal_entries.details.description'),
                ]) !!}
            </div>

        </div>{{-- /row --}}
    </div>{{-- /card-body --}}
</div>{{-- /card --}}

{{-- ══════════════════════════════════════════════════════════════
     2. DETAILS TABLE SECTION
══════════════════════════════════════════════════════════════ --}}
<div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">

    {{-- Details header bar --}}
    <div class="card-header py-3 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            @lang('crud.detail')
        </h5>
        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-md-flex text-muted small align-items-center gap-1">
                <i class="fa-regular fa-keyboard"></i>
                <kbd>Ctrl+Enter</kbd>
                @lang('crud.add_item')
            </span>
            <button type="button" id="add-row" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                <i class="fa fa-plus"></i>
                @lang('crud.add_item')
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="journal-details-table">
                <thead>
                    <tr class="text-center table-light align-middle text-secondary fw-semibold">
                        <th style="width:4%; min-width:42px;">
                            #
                        </th>
                        <th style="width:26%; min-width:180px;" class="text-start">
                            @lang('accusoft::models/as_journal_entries.details.tree_account_id')
                            <span class="text-danger">*</span>
                        </th>
                        <th style="width:15%; min-width:130px;">
                            @lang('accusoft::models/as_journal_entries.details.cost_center_id')
                        </th>
                        <th style="width:13%; min-width:110px;">
                            @lang('accusoft::models/as_journal_entries.details.debit')
                        </th>
                        <th style="width:13%; min-width:110px;">
                            @lang('accusoft::models/as_journal_entries.details.credit')
                        </th>
                        <th style="width:21%; min-width:140px;" class="text-start">
                            @lang('accusoft::models/as_journal_entries.details.description')
                        </th>
                        <th style="width:8%; min-width:70px;">
                            <i class="fa-solid fa-gear"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rowsCount = old('details')
                            ? count(old('details'))
                            : (isset($journalEntry) && $journalEntry->details->count() > 0
                                ? $journalEntry->details->count()
                                : 2);
                    @endphp

                    @for ($index = 0; $index < $rowsCount; $index++)
                        @php
                            $detail = isset($journalEntry) ? $journalEntry->details->values()->get($index) : null;
                        @endphp
                        <tr data-row="{{ $index }}">
                            <td class="text-center">
                                <span class="row-num-badge row-number"></span>
                            </td>

                            {{-- Account --}}
                            <td>
                                <x-select2-account
                                    name="details[{{ $index }}][tree_account_id]"
                                    :placeholder="__('accusoft::models/as_journal_entries.messages.select_account')"
                                    :selected_id="old('details.' . $index . '.tree_account_id', $detail ? $detail->tree_account_id : null)"
                                    class="account-select" />
                            </td>

                            {{-- Cost Center --}}
                            <td>
                                <select name="details[{{ $index }}][cost_center_id]"
                                        class="form-control cost-center-select"
                                        data-placeholder="@lang('crud.optional')">
                                    <option value=""></option>
                                    @foreach ($CostCenters ?? [] as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('details.' . $index . '.cost_center_id', $detail ? $detail->cost_center_id : null) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Debit --}}
                            <td>
                                {!! Form::number(
                                    "details[$index][debit]",
                                    old('details.' . $index . '.debit',
                                        $detail && $detail->debit > 0 ? $detail->debit : null),
                                    [
                                        'class'       => 'form-control text-end debit',
                                        'step'        => '0.01',
                                        'min'         => '0',
                                        'placeholder' => '0.00',
                                    ]
                                ) !!}
                            </td>

                            {{-- Credit --}}
                            <td>
                                {!! Form::number(
                                    "details[$index][credit]",
                                    old('details.' . $index . '.credit',
                                        $detail && $detail->credit > 0 ? $detail->credit : null),
                                    [
                                        'class'       => 'form-control text-end credit',
                                        'step'        => '0.01',
                                        'min'         => '0',
                                        'placeholder' => '0.00',
                                    ]
                                ) !!}
                            </td>

                            {{-- Description --}}
                            <td>
                                {!! Form::text(
                                    "details[$index][description]",
                                    old('details.' . $index . '.description',
                                        $detail ? $detail->description : null),
                                    [
                                        'class'       => 'form-control',
                                        'placeholder' => __('lang.notes'),
                                    ]
                                ) !!}
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-icon btn-light-primary copy-row" title="@lang('crud.copy')">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-row" title="@lang('crud.delete')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>

                {{-- Totals Footer --}}
                <tfoot class="bg-light-soft border-top border-2">
                    <tr>
                        <td colspan="3" class="text-end fw-bold text-muted">
                            <i class="fa-solid fa-sigma me-1"></i>
                            @lang('accusoft::models/as_journal_entries.fields.total'):
                        </td>
                        <td>
                            <input type="text" id="total_debit"
                                   class="form-control text-end fw-bold je-total-debit"
                                   readonly placeholder="0.00">
                        </td>
                        <td>
                            <input type="text" id="total_credit"
                                   class="form-control text-end fw-bold je-total-credit"
                                   readonly placeholder="0.00">
                        </td>
                        <td colspan="2" id="balance-status" class="text-center align-middle"></td>
                    </tr>
                    {{-- Difference Row --}}
                    <tr id="difference-row" style="display:none;">
                        <td colspan="3" class="text-end text-muted fw-bold small">
                            <i class="fa-solid fa-scale-balanced me-1"></i>
                            @lang('accusoft::models/as_journal_entries.messages.difference'):
                        </td>
                        <td colspan="2">
                            <input type="text" id="total_difference"
                                   class="form-control text-end fw-bold"
                                   readonly placeholder="0.00"
                                   style="font-size:.82rem;">
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Hidden data for JavaScript --}}
<script id="cost-centers-data" type="application/json">
    @json($CostCenters ?? [])
</script>

{{-- Row Template --}}
<template id="detail-row-template">
    <tr data-row="__INDEX__">
        <td class="text-center">
            <span class="row-num-badge row-number"></span>
        </td>
        <td>
            <select name="details[__INDEX__][tree_account_id]"
                    class="form-control account-select select2-account"
                    data-placeholder="@lang('accusoft::models/as_journal_entries.messages.select_account')">
            </select>
        </td>
        <td>
            <select name="details[__INDEX__][cost_center_id]"
                    class="form-control cost-center-select"
                    data-placeholder="@lang('accusoft::models/as_journal_entries.messages.optional')">
                <option value=""></option>
            </select>
        </td>
        <td>
            <input type="number" name="details[__INDEX__][debit]"
                   class="form-control text-end debit"
                   step="0.01" min="0" placeholder="0.00">
        </td>
        <td>
            <input type="number" name="details[__INDEX__][credit]"
                   class="form-control text-end credit"
                   step="0.01" min="0" placeholder="0.00">
        </td>
        <td>
            <input type="text" name="details[__INDEX__][description]"
                   class="form-control"
                   placeholder="@lang('lang.notes')">
        </td>
        <td class="text-center">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-icon btn-light-primary copy-row" title="@lang('crud.copy')">
                    <i class="fa-solid fa-copy"></i>
                </button>
                <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-row" title="@lang('crud.delete')">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
</template>

@push('scripts')
<script>
if (!window.journalEntryScriptsInitialized) {
    window.journalEntryScriptsInitialized = true;

    document.addEventListener('DOMContentLoaded', function () {
        /* ══════════════════════════════════════════════════════════════════
           1. DOM REFERENCES & CONSTANTS
        ══════════════════════════════════════════════════════════════════ */
        const tableBody        = document.querySelector('#journal-details-table tbody');
        const submitBtn        = document.querySelector('#submit-btn');
        const totalDebitField  = document.getElementById('total_debit');
        const totalCreditField = document.getElementById('total_credit');
        const balanceStatus    = document.getElementById('balance-status');

        const LANG                = '{{ app()->getLocale() }}';
        const ACCOUNTS_LOOKUP_URL = '{{ route("Lookup.TreeAccounts") }}';

        // Global cache for loaded accounts
        window.accountsCache = window.accountsCache || {};

        // Load Cost Centers list from JSON script tag
        let costCentersData = {};
        try {
            const ccEl = document.getElementById('cost-centers-data');
            if (ccEl) costCentersData = JSON.parse(ccEl.textContent);
        } catch (_) {}

        // rowIndex calculated safely to avoid duplicate conflicts
        let rowIndex = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const idx = parseInt(row.dataset.row);
            if (!isNaN(idx) && idx >= rowIndex) rowIndex = idx + 1;
        });

        /* ══════════════════════════════════════════════════════════════════
           2. UTILITY FUNCTIONS
        ══════════════════════════════════════════════════════════════════ */

        function updateRowNumbers() {
            tableBody.querySelectorAll('tr').forEach((row, i) => {
                const badge = row.querySelector('.row-number');
                if (badge) badge.textContent = i + 1;
            });
        }

        function calculateTotals() {
            let totalDebit = 0, totalCredit = 0;
            tableBody.querySelectorAll('tr').forEach(row => {
                // Read value even if field is disabled (use data-value or actual value)
                const dEl = row.querySelector('.debit');
                const cEl = row.querySelector('.credit');
                totalDebit  += parseFloat(dEl?.value)  || 0;
                totalCredit += parseFloat(cEl?.value) || 0;
            });
            if (totalDebitField)  totalDebitField.value  = totalDebit.toFixed(2);
            if (totalCreditField) totalCreditField.value = totalCredit.toFixed(2);

            const count = tableBody.querySelectorAll('tr').length;
            // Use absolute difference; tolerance 0.001 to handle float rounding
            const diff  = Math.round((totalDebit - totalCredit) * 1000) / 1000;
            const absDiff = Math.abs(diff);

            // Show/hide difference row
            const diffRow  = document.getElementById('difference-row');
            const diffField = document.getElementById('total_difference');
            if (diffRow && diffField) {
                if (absDiff > 0.001 && totalDebit > 0) {
                    diffRow.style.display = '';
                    const debitExtra = '@lang("accusoft::models/as_journal_entries.messages.debit_extra")';
                    const creditExtra = '@lang("accusoft::models/as_journal_entries.messages.credit_extra")';
                    diffField.value = diff > 0
                        ? `+${absDiff.toFixed(2)} (${debitExtra})`
                        : `-${absDiff.toFixed(2)} (${creditExtra})`;
                    diffField.style.color = diff > 0 ? '#1a7a4a' : '#c0392b';
                } else {
                    diffRow.style.display = 'none';
                }
            }

            if (!balanceStatus) return;

            if (count >= 2 && absDiff <= 0.001 && totalDebit > 0) {
                // ✅ Balanced
                balanceStatus.innerHTML = '<span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>@lang('accusoft::models/as_journal_entries.balanced')</span>';
                if (submitBtn) submitBtn.disabled = false;
            } else if (totalDebit === 0 && totalCredit === 0) {
                // ⚠️ No amounts entered yet
                balanceStatus.innerHTML = '<span class="badge bg-secondary"><i class="fa-solid fa-minus me-1"></i>@lang("accusoft::models/as_journal_entries.messages.no_amounts_entered")</span>';
                if (submitBtn) submitBtn.disabled = true;
            } else if (count < 2) {
                // ⚠️ Not enough rows
                balanceStatus.innerHTML = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>@lang("accusoft::models/as_journal_entries.messages.min_rows")</span>';
                if (submitBtn) submitBtn.disabled = true;
            } else {
                // ❌ Unbalanced - show actual difference
                const debitExceeds = '@lang("accusoft::models/as_journal_entries.messages.debit_exceeds")';
                const creditExceeds = '@lang("accusoft::models/as_journal_entries.messages.credit_exceeds")';
                const label = diff > 0 ? debitExceeds : creditExceeds;
                balanceStatus.innerHTML = `<span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>@lang("accusoft::models/as_journal_entries.unbalanced") | ${absDiff.toFixed(2)} ${label}</span>`;
                if (submitBtn) submitBtn.disabled = true;
            }
        }

        function populateCostCenterSelect(selectEl) {
            selectEl.innerHTML = '<option value=""></option>';
            Object.entries(costCentersData).forEach(([id, name]) => {
                const opt = document.createElement('option');
                opt.value       = id;
                opt.textContent = name;
                selectEl.appendChild(opt);
            });
        }

        /* ══════════════════════════════════════════════════════════════════
           3. UNIFIED initRow BOOTSTRAP
        ══════════════════════════════════════════════════════════════════ */

        function initRow(rowEl, data = {}) {
            const $row    = $(rowEl);
            const $accSel = $row.find('.account-select');
            const $ccSel  = $row.find('.cost-center-select');

            // ── Cost Center Select2 Setup ──────────────────────────────────
            const ccVal = data.cost_center_id || $ccSel.data('selected') || $ccSel.val();
            populateCostCenterSelect($ccSel[0]);
            if (ccVal) $ccSel.val(ccVal);

            if ($ccSel.data('select2')) {
                try { $ccSel.select2('destroy'); } catch (_) {}
            }
            $ccSel.select2({
                placeholder : '@lang('accusoft::models/as_journal_entries.messages.optional')',
                allowClear  : true,
                dir         : 'rtl',
                width       : '100%',
                language    : { noResults: () => '@lang('accusoft::models/as_journal_entries.messages.no_results')' }
            });

            // ── Account Select2 Setup ──────────────────────────────────────
            const targetAccId = data.tree_account_id || $accSel.data('selected') || $accSel.val();

            if ($accSel.data('select2')) {
                try { $accSel.select2('destroy'); } catch (_) {}
            }

            const proceedInitAccountSelect2 = () => {
                $accSel.select2({
                    ajax: {
                        url     : ACCOUNTS_LOOKUP_URL,
                        dataType: 'json',
                        delay   : 250,
                        cache   : true,
                        data    : params => ({
                            search : params.term || '',
                            page   : params.page || 1,
                            lang   : LANG,
                        }),
                        processResults(data) {
                            (data.results || []).forEach(item => {
                                window.accountsCache[item.id] = item;
                            });
                            return {
                                results   : data.results || [],
                                pagination: { more: data.pagination?.more || false }
                            };
                        }
                    },
                    placeholder       : '@lang('accusoft::models/as_journal_entries.messages.select_account')',
                    allowClear        : true,
                    minimumInputLength: 0,
                    dir               : 'rtl',
                    width             : '100%',
                    language: {
                        searching  : () => '@lang('accusoft::models/as_journal_entries.messages.searching')',
                        noResults  : () => '@lang('accusoft::models/as_journal_entries.messages.no_results')',
                        loadingMore: () => '@lang('accusoft::models/as_journal_entries.messages.loading_more')'
                    }
                });
            };

            if (targetAccId) {
                const cached = window.accountsCache[targetAccId];
                if (cached) {
                    $accSel.html('').append(new Option(cached.text, cached.id, true, true));
                    proceedInitAccountSelect2();
                } else {
                    $.ajax({
                        url     : ACCOUNTS_LOOKUP_URL,
                        data    : { id: targetAccId, lang: LANG },
                        dataType: 'json',
                        success : function(res) {
                            if (res.results && res.results.length > 0) {
                                const acc = res.results[0];
                                window.accountsCache[acc.id] = acc;
                                $accSel.html('').append(new Option(acc.text, acc.id, true, true));
                            }
                            proceedInitAccountSelect2();
                        },
                        error: function() { proceedInitAccountSelect2(); }
                    });
                }
            } else {
                $accSel.html('<option value=""></option>');
                proceedInitAccountSelect2();
            }
        }

        /* ══════════════════════════════════════════════════════════════════
           4. PRELOAD CHOSEN ACCOUNTS IN BATCH
        ══════════════════════════════════════════════════════════════════ */

        function preloadInitialAccounts(callback) {
            const ids = [];
            tableBody.querySelectorAll('.account-select').forEach(el => {
                const id = el.dataset.selected || $(el).val();
                if (id && !ids.includes(id)) ids.push(id);
            });

            if (ids.length === 0) { if (callback) callback(); return; }

            $.ajax({
                url     : ACCOUNTS_LOOKUP_URL,
                data    : { ids: ids, lang: LANG },
                dataType: 'json',
                success : function(res) {
                    if (res.results) {
                        res.results.forEach(acc => { window.accountsCache[acc.id] = acc; });
                    }
                    if (callback) callback();
                },
                error: function() { if (callback) callback(); }
            });
        }

        /* ══════════════════════════════════════════════════════════════════
           5. BIND EVENTS & HANDLERS
        ══════════════════════════════════════════════════════════════════ */

        // ── Bootstrap existing rows ────────────────────────────────────
        preloadInitialAccounts(function() {
            tableBody.querySelectorAll('tr').forEach(row => {
                initRow(row);
                const d = row.querySelector('.debit');
                const c = row.querySelector('.credit');
                // Store original name as data-name to restore if re-enabled
                if (d) d.dataset.name = d.name;
                if (c) c.dataset.name = c.name;
                // If debit has value → disable credit (and vice versa)
                if (parseFloat(d?.value) > 0 && c) {
                    c.disabled = true;
                    c.value = '';
                } else if (parseFloat(c?.value) > 0 && d) {
                    d.disabled = true;
                    d.value = '';
                }
                
                // Trigger validation for pre-filled data (e.g. edit/old)
                $(row).find('.account-select').trigger('change');
            });
            updateRowNumbers();
            calculateTotals();
        });

        // ── Add row ────────────────────────────────────────────────────
        document.getElementById('add-row')?.addEventListener('click', function () {
            const template    = document.getElementById('detail-row-template');
            const fragment    = template.content.cloneNode(true);
            const newRow      = fragment.querySelector('tr');
            newRow.innerHTML  = newRow.innerHTML.replace(/__INDEX__/g, rowIndex);
            newRow.dataset.row = rowIndex;
            tableBody.appendChild(newRow);

            const appendedRow = tableBody.querySelector(`tr[data-row="${rowIndex}"]`);

            // Store data-name on debit/credit for future restore
            const d = appendedRow.querySelector('.debit');
            const c = appendedRow.querySelector('.credit');
            if (d) d.dataset.name = d.name;
            if (c) c.dataset.name = c.name;

            initRow(appendedRow);

            rowIndex++;
            updateRowNumbers();
            calculateTotals();

            appendedRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            // Open account dropdown automatically
            setTimeout(() => {
                const $acc = $(appendedRow).find('.account-select');
                if ($acc.hasClass('select2-hidden-accessible')) $acc.select2('open');
            }, 150);
        });

        // ── Copy & Remove row (event delegation) ──────────────────────
        tableBody.addEventListener('click', function (e) {
            // Copy row
            const copyBtn = e.target.closest('.copy-row');
            if (copyBtn) {
                const sourceRow = copyBtn.closest('tr');
                const srcAccId  = $(sourceRow).find('.account-select').val();
                const srcCcId   = $(sourceRow).find('.cost-center-select').val();
                const srcDebit  = sourceRow.querySelector('.debit')?.value  || '';
                const srcCredit = sourceRow.querySelector('.credit')?.value || '';
                const srcDesc   = sourceRow.querySelector('input[name*="[description]"]')?.value || '';

                const template    = document.getElementById('detail-row-template');
                const fragment    = template.content.cloneNode(true);
                const newRow      = fragment.querySelector('tr');
                newRow.innerHTML  = newRow.innerHTML.replace(/__INDEX__/g, rowIndex);
                newRow.dataset.row = rowIndex;
                tableBody.appendChild(newRow);

                const appendedRow = tableBody.querySelector(`tr[data-row="${rowIndex}"]`);

                // Store data-name before initRow
                const nd = appendedRow.querySelector('.debit');
                const nc = appendedRow.querySelector('.credit');
                if (nd) nd.dataset.name = nd.name;
                if (nc) nc.dataset.name = nc.name;

                initRow(appendedRow, { tree_account_id: srcAccId, cost_center_id: srcCcId });

                // Copy amounts with proper mutual exclusion
                if (srcDebit && parseFloat(srcDebit) > 0) {
                    nd.value = srcDebit;
                    nc.disabled = true;
                    nc.value = '';
                } else if (srcCredit && parseFloat(srcCredit) > 0) {
                    nc.value = srcCredit;
                    nd.disabled = true;
                    nd.value = '';
                }

                const descInput = appendedRow.querySelector('input[name*="[description]"]');
                if (descInput) descInput.value = srcDesc;

                rowIndex++;
                updateRowNumbers();
                calculateTotals();
                appendedRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }

            // Remove row
            const removeBtn = e.target.closest('.remove-row');
            if (removeBtn) {
                if (tableBody.querySelectorAll('tr').length <= 2) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon             : 'warning',
                            title            : '@lang('accusoft::models/as_journal_entries.messages.alert')',
                            text             : '@lang('accusoft::models/as_journal_entries.messages.min_rows_alert')',
                            confirmButtonText: '@lang('accusoft::models/as_journal_entries.messages.ok')'
                        });
                    } else {
                        alert('@lang('accusoft::models/as_journal_entries.messages.min_rows_alert')');
                    }
                    return;
                }
                const row = removeBtn.closest('tr');
                $(row).find('.select2-hidden-accessible').each(function () {
                    try { $(this).select2('destroy'); } catch (_) {}
                });
                row.remove();
                updateRowNumbers();
                calculateTotals();
            }
        });

        // ── Debit & Credit Mutual Exclusion (محاسبياً: سطر واحد إما مدين أو دائن) ────
        tableBody.addEventListener('input', function (e) {
            const t = e.target;
            if (!t.classList.contains('debit') && !t.classList.contains('credit')) return;

            const row = t.closest('tr');
            const d   = row.querySelector('.debit');
            const c   = row.querySelector('.credit');
            const val = parseFloat(t.value) || 0;

            if (t.classList.contains('debit')) {
                if (val > 0) {
                    // Has debit → disable credit and clear it
                    c.disabled = true;
                    c.value = '';
                    // Re-enable debit's hidden companion (for form submission)
                    c.removeAttribute('name'); // don't send disabled credit
                } else {
                    // Debit cleared → re-enable credit
                    c.disabled = false;
                    c.setAttribute('name', c.getAttribute('data-name') || c.name || d.name.replace('[debit]', '[credit]'));
                }
            } else {
                if (val > 0) {
                    d.disabled = true;
                    d.value = '';
                    d.removeAttribute('name');
                } else {
                    d.disabled = false;
                    d.setAttribute('name', d.getAttribute('data-name') || d.name || c.name.replace('[credit]', '[debit]'));
                }
            }

            calculateTotals();
        });

        // ── Cost-center validation on change ──────────────────────────
        $(tableBody).on('change', '.account-select, .cost-center-select', function () {
            const $row      = $(this).closest('tr');
            const accountId = $row.find('.account-select').val();
            const $ccSel    = $row.find('.cost-center-select');
            const ccId      = $ccSel.val();

            $ccSel.removeClass('is-invalid');
            $ccSel.next('.select2-container').removeClass('is-invalid border border-danger');
            $row.find('.cost-center-error').remove();

            if (accountId && window.accountsCache[accountId]) {
                const acc = window.accountsCache[accountId];
                if ((acc.cost_center == 1 || acc.cost_center === true) && !ccId) {
                    $ccSel.addClass('is-invalid');
                    $ccSel.next('.select2-container').addClass('is-invalid border border-danger');
                    $ccSel.closest('td').append(
                        '<div class="invalid-feedback d-block cost-center-error text-danger mt-1 fs-8">@lang("accusoft::models/as_journal_entries.messages.cost_center_required")</div>'
                    );
                }
            }
        });

        // ── Re-enable disabled fields before submit (so values are sent) ────────
        // Disabled inputs are NOT submitted by the browser. We re-enable them
        // right before submit so the server receives all values.
        const form = tableBody.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {

                // 1. Re-enable all disabled debit/credit fields so they submit
                tableBody.querySelectorAll('.debit[disabled], .credit[disabled]').forEach(el => {
                    el.disabled = false;
                    // Value is already 0/empty so server correctly ignores it
                });

                // 2. Clear previous cost-center errors
                document.querySelectorAll('.cost-center-error').forEach(el => el.remove());
                $('.cost-center-select')
                    .removeClass('is-invalid')
                    .next('.select2-container')
                    .removeClass('is-invalid border border-danger');

                // 3. Accounting validation: each row must have account + at least one amount
                const errors = [];
                const missing = []; // cost-center missing

                const rowText = '@lang("accusoft::models/as_journal_entries.messages.row")';
                const accountRequiredTxt = '@lang("accusoft::models/as_journal_entries.messages.account_required_for_amount")';
                const noBothTxt = '@lang("accusoft::models/as_journal_entries.messages.no_both_debit_credit")';

                tableBody.querySelectorAll('tr').forEach((row, idx) => {
                    const accountId = row.querySelector('.account-select')?.value;
                    const debit     = parseFloat(row.querySelector('.debit')?.value)  || 0;
                    const credit    = parseFloat(row.querySelector('.credit')?.value) || 0;
                    const ccId      = row.querySelector('.cost-center-select')?.value;

                    // 3a. Account is required if any amount is entered
                    if ((debit > 0 || credit > 0) && !accountId) {
                        errors.push(`${rowText} ${idx + 1}: ${accountRequiredTxt}`);
                        $(row).find('.account-select').next('.select2-container')
                            .addClass('border border-danger');
                    }

                    // 3b. Cannot have both debit and credit on same row
                    if (debit > 0 && credit > 0) {
                        errors.push(`${rowText} ${idx + 1}: ${noBothTxt}`);
                    }

                    // 3c. Cost center required check
                    if (accountId && window.accountsCache[accountId]) {
                        const acc = window.accountsCache[accountId];
                        if ((acc.cost_center == 1 || acc.cost_center === true) && !ccId) {
                            const name = acc.text || acc.name || `#${accountId}`;
                            if (!missing.includes(name)) missing.push(name);

                            const $ccSel = $(row).find('.cost-center-select');
                            $ccSel.addClass('is-invalid');
                            $ccSel.next('.select2-container').addClass('is-invalid border border-danger');

                            const err = document.createElement('div');
                            err.className   = 'invalid-feedback d-block cost-center-error text-danger mt-1 fs-8';
                            err.textContent = '@lang("accusoft::models/as_journal_entries.messages.cost_center_required")';
                            $ccSel[0].closest('td').appendChild(err);
                        }
                    }
                });

                // 4. Combine all errors
                if (missing.length > 0) {
                    errors.push('@lang("accusoft::models/as_journal_entries.messages.cost_center_missing")' + missing.join('، '));
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon             : 'error',
                            title            : '@lang('accusoft::models/as_journal_entries.messages.alert')',
                            html             : '<ul class="text-start mb-0">' +
                                               errors.map(err => `<li>${err}</li>`).join('') +
                                               '</ul>',
                            confirmButtonText: '@lang('accusoft::models/as_journal_entries.messages.ok')'
                        });
                    } else {
                        alert(errors.join('\n'));
                    }
                    return;
                }
            });
        }

        // ── Keyboard Shortcuts ─────────────────────────────────────────
        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('add-row')?.click();
            }
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                if (submitBtn && !submitBtn.disabled) submitBtn.click();
            }
        });

        // Enter key shifts focus to next input
        document.querySelectorAll('input:not([type="submit"]), select, textarea').forEach(input => {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    const all = Array.from(document.querySelectorAll(
                        'input:not([type="hidden"]):not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled])'
                    ));
                    const idx = all.indexOf(e.target);
                    if (idx > -1 && idx < all.length - 1) all[idx + 1].focus();
                }
            });
        });
    });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const entryDateEl = document.getElementById('kt_td_picker_entry_date');
    if (!entryDateEl) return;
    new tempusDominus.TempusDominus(entryDateEl, {
        localization: { locale: 'en', format: 'yyyy-MM-dd' },
        display: {
            components: {
                calendar: true, date: true, month: true, year: true,
                decades: false, clock: false, hours: false, minutes: false, seconds: false,
            },
            buttons: { today: true, clear: true, close: true },
        },
    });
});
</script>
@endpush
