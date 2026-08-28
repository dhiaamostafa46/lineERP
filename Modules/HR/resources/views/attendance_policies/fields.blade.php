<!-- Modern Enhanced Form Design -->
<div class="attendance-policy-form">

    <!-- Section 1: Basic Information -->
    <div class="form-section">


        <!-- Name Fields -->
        <div class="row">
            @foreach (config('langs') as $locale => $language)
                <div class="form-group col-sm-6 mb-3">
                    <label class="modern-label">

                        {{ $language }} @lang('hr::models/hr_attendance_policies.fields.name')
                    </label>
                    {!! Form::text($locale . '[name]', isset($policy) ? $policy->translate($locale)->name : null, [
                        'class' => 'form-control modern-input',
                        'placeholder' => __('hr::lang.enter_name')
                    ]) !!}
                </div>
            @endforeach
        </div>

        <!-- Description Field -->
        <div class="row">
            <div class="form-group col-12 mb-3">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.description')
                </label>
                {!! Form::textarea('description', null, ['class' => 'form-control modern-textarea', 'rows' => 4, 'placeholder' => __('hr::lang.enter_description')]) !!}
            </div>
        </div>

        <!-- Status and Automatic Row -->
        <div class="row">
            <div class="form-group col-lg-6 col-md-6 mb-3">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.status')
                </label>
                <div class="toggle-switch-group">
                    @foreach($statuses as $value => $label)
                        <input type="radio" class="toggle-check" name="status" id="status_{{$value}}" value="{{$value}}" {{ (isset($policy) ? $policy->status == $value : $value == 1) ? 'checked' : '' }}>
                        <label class="toggle-label" for="status_{{$value}}">
                            <i class="fas {{ $value == 1 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            <span>{{$label}}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group col-lg-6 col-md-6 mb-3">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.is_automatic')
                </label>
                <div class="toggle-switch-group">
                    @foreach($automatic as $value => $label)
                        <input type="radio" class="toggle-check" name="is_automatic" id="is_automatic_{{$value}}" value="{{$value}}" {{ (isset($policy) ? $policy->is_automatic == $value : $value == 1) ? 'checked' : '' }}>
                        <label class="toggle-label" for="is_automatic_{{$value}}">
                            <i class="fas {{ $value == 1 ? 'fa-sync-alt' : 'fa-hand-paper' }}"></i>
                            <span>{{$label}}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Policy Type & Scope -->
    <div class="form-section">

        <!-- Type and Scope Row -->
        <div class="row">
            <!-- Type Field -->
            <div class="form-group col-lg-6 mb-4">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.type')
                </label>
                <div class="modern-radio-group">
                    @foreach($types as $value => $label)
                        <input type="radio" class="btn-check" name="type" id="type_{{$value}}" value="{{$value}}" {{ (isset($policy) ? $policy->type == $value : $loop->first) ? 'checked' : '' }} autocomplete="off">
                        <label class="modern-radio-btn" for="type_{{$value}}">
                            @if($value == 1)
                                <i class="fas fa-user-times"></i>
                            @elseif($value == 2)
                                <i class="fas fa-clock"></i>
                            @elseif($value == 3)
                                <i class="fas fa-door-open"></i>
                            @else
                                <i class="fas fa-business-time"></i>
                            @endif
                            <span>{{$label}}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Scope Field -->
            <div class="form-group col-lg-6 mb-4">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.scope')
                </label>
                <div class="modern-radio-group">
                    @foreach($scopes as $value => $label)
                        <input type="radio" class="btn-check" name="scope" id="scope_{{$value}}" value="{{$value}}" {{ (isset($policy) ? $policy->scope == $value : $loop->first) ? 'checked' : '' }} autocomplete="off">
                        <label class="modern-radio-btn secondary" for="scope_{{$value}}">
                            @if($value == 1)
                                <i class="fas fa-user"></i>
                            @elseif($value == 2)
                                <i class="fas fa-building"></i>
                            @elseif($value == 3)
                                <i class="fas fa-briefcase"></i>
                            @else
                                <i class="fas fa-map-marker-alt"></i>
                            @endif
                            <span>{{$label}}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Scope IDs Fields -->
        <div class="row scope-details">
            <div class="form-group col-12 mb-3 scope-field" id="scope_employee" style="display: none;">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.scopes.employee')
                </label>
                <x-select2multi-input id="scope_ids_employee" name="scope_ids[]" :placeholder="__('hr::lang.select_employee')" :list="$employees"
                    :selected_id="old('scope_ids', isset($policy) && $policy->scope == 1 ? $policy->scope_ids_list : [])" />
            </div>

            <div class="form-group col-12 mb-3 scope-field" id="scope_department" style="display: none;">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.scopes.department')
                </label>
                <x-select2multi-input id="scope_ids_department" name="scope_ids[]" :placeholder="__('hr::lang.select_department')" :list="$departments"
                    :selected_id="old('scope_ids', isset($policy) && $policy->scope == 2 ? $policy->scope_ids_list : [])" />
            </div>

            <div class="form-group col-12 mb-3 scope-field" id="scope_job" style="display: none;">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.scopes.job')
                </label>
                <x-select2multi-input id="scope_ids_job" name="scope_ids[]" :placeholder="__('hr::lang.select_job')" :list="$jobs"
                    :selected_id="old('scope_ids', isset($policy) && $policy->scope == 3 ? $policy->scope_ids_list : [])" />
            </div>

            <div class="form-group col-12 mb-3 scope-field" id="scope_branch" style="display: none;">
                <label class="modern-label">
                
                    @lang('hr::models/hr_attendance_policies.scopes.branch')
                </label>
                <x-select2multi-input id="scope_ids_branch" name="scope_ids[]" :placeholder="__('hr::lang.select_branch')" :list="$branches"
                    :selected_id="old('scope_ids', isset($policy) && $policy->scope == 4 ? $policy->scope_ids_list : [])" />
            </div>
        </div>

        <!-- Calculation Type and Salary Effect Row -->
        <div class="row">
            <div class="form-group col-lg-6 mb-3" id="calculation_type_div" style="display: none;">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.calculation_type')
                </label>
                <div class="toggle-switch-group">
                    @foreach($calculationType as $value => $label)
                        <input type="radio" class="toggle-check" name="calculation_type" id="calculation_type_{{$value}}" value="{{$value}}" {{ (isset($policy) ? $policy->calculation_type == $value : $loop->first) ? 'checked' : '' }}>
                        <label class="toggle-label" for="calculation_type_{{$value}}">
                            <i class="fas {{ $value == 'day' ? 'fa-calendar-day' : 'fa-clock' }}"></i>
                            <span>{{$label}}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group col-lg-6 mb-3" id="salary_effectdiv" style="display: none;">
                <label class="modern-label">

                    @lang('hr::models/hr_attendance_policies.fields.salary_effect')
                </label>
                <div class="checkbox-pills">
                    @foreach($salarys as $key => $label)
                        @if($key === 'basic')
                            <input class="checkbox-pill-input" type="checkbox" name="salary_effect[basic]" id="salary_effect_basic" value="1" {{ old('salary_effect.basic', isset($policy) && $policy->salary_effect_basic) ? 'checked' : '' }}>
                            <label class="checkbox-pill" for="salary_effect_basic">
                                <i class="fas fa-coins"></i>
                                <span>{{ $label }}</span>
                            </label>
                        @else
                            <input class="checkbox-pill-input" type="checkbox" name="salary_effect[allowances][]" id="salary_effect_allowance_{{$key}}" value="{{$key}}" {{ old('salary_effect.allowances') ? (in_array($key, old('salary_effect.allowances')) ? 'checked' : '') : (isset($policy) && in_array($key, $policy->salary_effect_allowances) ? 'checked' : '') }}>
                            <label class="checkbox-pill" for="salary_effect_allowance_{{$key}}">
                                <i class="fas fa-plus-circle"></i>
                                <span>{{ $label }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Policy Rules -->
    <div class="form-section mt-2">
        {{-- <div class="section-header">
            <i class="fas fa-gavel"></i>
            <h5>@lang('hr::models/hr_attendance_policies.rules_settings')</h5>
        </div> --}}

        <!-- Saudi Penalties System for Delays -->
        <div id="delay_saudi_rules" style="display:none;">
            <div class="alert-card warning">
                <div class="alert-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h6>@lang('hr::models/hr_attendance_policies.saudi_penalties.title')</h6>
                </div>
                <ul class="alert-list">
                    <li>@lang('hr::models/hr_attendance_policies.saudi_penalties.delay_info_1')</li>
                    <li>@lang('hr::models/hr_attendance_policies.saudi_penalties.delay_info_2')</li>
                    <li>@lang('hr::models/hr_attendance_policies.saudi_penalties.delay_info_3')</li>
                </ul>

                 <div class="penalties-table-wrapper">
                <div class="table-header">
                    <i class="fas fa-table me-2"></i>
                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.deduction')</span>
                </div>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>@lang('hr::models/hr_attendance_policies.saudi_penalties.time_range')</th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.first')
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.second')
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.third')
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.fourth')
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['0_15' => 'late_15', '15_30' => 'late_15_30', '30_60' => 'late_30_60', '60_plus' => 'late_60_plus', 'early_15' => 'early_exit_15', 'early_15_plus' => 'early_exit_15_plus'] as $key => $label)
                                <tr>
                                    <td class="row-label">
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.violations.' . $label)
                                    </td>
                                    <td>{!! Form::number("settings[delay][$key][daily][first]", $policy->settings['delay'][$key]['daily']['first'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                    <td>{!! Form::number("settings[delay][$key][daily][second]", $policy->settings['delay'][$key]['daily']['second'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                    <td>{!! Form::number("settings[delay][$key][daily][third]", $policy->settings['delay'][$key]['daily']['third'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                    <td>{!! Form::number("settings[delay][$key][daily][fourth]", $policy->settings['delay'][$key]['daily']['fourth'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            </div>


        </div>

        <!-- Absence Rules (Saudi System) -->
        <div id="absence_saudi_rules" style="display:none;">
            <div class="alert-card danger">
                <div class="alert-header">
                    <i class="fas fa-user-times"></i>
                    <h6>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_title')</h6>
                </div>
                <ul class="alert-list">
                    <li>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_info_1')</li>
                    <li>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_info_2')</li>
                    <li>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_info_3')</li>
                </ul>
                  <div class="penalties-table-wrapper">
                <div class="table-responsive">
                    <table class="modern-table compact">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.first')
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.second')
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.third')
                                    </div>
                                </th>
                                <th class="text-center">
                                    <div class="th-content">

                                        @lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.fourth')
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{!! Form::number('settings[absence][first]', $policy->settings['absence']['first'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                <td>{!! Form::number('settings[absence][second]', $policy->settings['absence']['second'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                <td>{!! Form::number('settings[absence][third]', $policy->settings['absence']['third'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                                <td>{!! Form::number('settings[absence][fourth]', $policy->settings['absence']['fourth'] ?? 0, ['class' => 'form-control table-input', 'step' => '0.01']) !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>


        </div>

        <!-- Overtime Rules -->
        <div id="overtime_rules" style="display:none;">
            <div class="alert-card success">
                <div class="alert-header">
                    <i class="fas fa-business-time"></i>
                    <h6>@lang('hr::models/hr_attendance_policies.rules.overtime_rate')</h6>
                </div>
                <div class="alert-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="modern-label">
                                <i class="fas fa-percentage me-1"></i>
                                @lang('hr::models/hr_attendance_policies.rules.overtime_rate')
                            </label>
                            <div class="input-icon-group">
                                <span class="input-icon">
                                    <i class="fas fa-calculator"></i>
                                </span>
                                {!! Form::number('settings[overtime_rate]', $policy->settings['overtime_rate'] ?? 1.5, ['class' => 'form-control modern-input with-icon', 'step' => '0.01', 'placeholder' => '1.5']) !!}
                            </div>
                            <small class="form-hint">@lang('hr::lang.overtime_rate_hint')</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Form Container */
.attendance-policy-form {
    max-width: 1400px;
    margin: 0 auto;
}

/* Section Styling */
/* .form-section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 24px;
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.form-section:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
} */

/* .section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 16px;
    margin-bottom: 24px;
    border-bottom: 2px solid #f0f0f0;
} */

.section-header i {
    font-size: 24px;
    color: #0d6efd;
}

.section-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

/* Modern Labels */
.modern-label {
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.modern-label i {
    color: #6c757d;
    font-size: 14px;
}

/* Modern Input */
.modern-input, .modern-textarea {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 14px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.modern-input:focus, .modern-textarea:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

/* Modern Radio Group */
.modern-radio-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.modern-radio-btn {
    flex: 1;
    min-width: 120px;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    text-align: center;
}

.modern-radio-btn i {
    font-size: 24px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.modern-radio-btn span {
    font-size: 13px;
    font-weight: 500;
    color: #495057;
}

.modern-radio-btn:hover {
    border-color: #0d6efd;
    background: #f8f9ff;
    transform: translateY(-2px);
}

.btn-check:checked + .modern-radio-btn {
    border-color: #0d6efd;
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-check:checked + .modern-radio-btn i,
.btn-check:checked + .modern-radio-btn span {
    color: #fff;
}

.btn-check:checked + .modern-radio-btn.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

/* Toggle Switch Group */
.toggle-switch-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.toggle-label {
    flex: 1;
    min-width: 80px;
    padding: 10px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
}

.toggle-label:hover {
    border-color: #adb5bd;
    background: #e9ecef;
}

.toggle-check:checked + .toggle-label {
    border-color: #198754;
    background: #198754;
    color: #fff;
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.3);
}

.toggle-check:checked + .toggle-label i {
    color: #fff;
}

/* Checkbox Pills */
.checkbox-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.checkbox-pill {
    padding: 8px 14px;
    border: 2px solid #dee2e6;
    border-radius: 20px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
}

.checkbox-pill:hover {
    border-color: #0d6efd;
    background: #f8f9ff;
}

.checkbox-pill-input:checked + .checkbox-pill {
    border-color: #0d6efd;
    background: #0d6efd;
    color: #fff;
}

.checkbox-pill-input:checked + .checkbox-pill i {
    color: #fff;
}

/* Alert Cards */
.alert-card {
    border-radius: 10px;
    margin-bottom: 24px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.alert-card.warning {
    border: 2px solid #ffc107;
}

.alert-card.danger {
    border: 2px solid #dc3545;
}

.alert-card.success {
    border: 2px solid #198754;
}

.alert-header {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-card.warning .alert-header {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
    color: #000;
}

.alert-card.danger .alert-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: #fff;
}

.alert-card.success .alert-header {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    color: #fff;
}

.alert-header i {
    font-size: 24px;
}

.alert-header h6 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.alert-list {
    list-style: none;
    padding: 16px 20px;
    margin: 0;
    background: #fff;
}

.alert-list li {
    padding: 8px 0;
    padding-left: 24px;
    position: relative;
    font-size: 14px;
    line-height: 1.6;
}

.alert-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    font-weight: bold;
    color: #198754;
}

.alert-body {
    padding: 20px;
    background: #fff;
}

/* Table Styling */
.penalties-table-wrapper {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
}

.table-header {
    background: #f8f9fa;
    padding: 12px 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.modern-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.modern-table th {
    padding: 14px 12px;
    font-weight: 600;
    font-size: 13px;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.modern-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.modern-table tbody tr {
    transition: all 0.2s ease;
}

.modern-table tbody tr:hover {
    background: #f8f9ff;
}

.modern-table .row-label {
    font-weight: 500;
    color: #495057;
}

.table-input {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 8px 12px;
    text-align: center;
    font-weight: 500;
    transition: all 0.3s ease;
}

.table-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
}

.th-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.th-content i {
    font-size: 16px;
    color: #0d6efd;
}

/* Input Icon Group */
.input-icon-group {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
}

.modern-input.with-icon {
    padding-left: 40px;
}

.form-hint {
    display: block;
    margin-top: 6px;
    color: #6c757d;
    font-size: 12px;
    font-style: italic;
}

/* Scope Details Animation */
.scope-details {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-radio-btn {
        min-width: 100px;
        padding: 10px 12px;
    }

    .modern-radio-btn i {
        font-size: 20px;
    }

    .modern-radio-btn span {
        font-size: 12px;
    }

    .form-section {
        padding: 16px;
    }

    .toggle-label {
        min-width: 70px;
        font-size: 12px;
    }
}

/* Hide default radio/checkbox */
.btn-check,
.toggle-check,
.checkbox-pill-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        function updateScopeFields() {
            var scope = $('input[name="scope"]:checked').val();
            $('.scope-field').hide().find('select, input').prop('disabled', true);

            if (scope == 1) {
                $('#scope_employee').show().find('select, input').prop('disabled', false);
            } else if (scope == 2) {
                $('#scope_department').show().find('select, input').prop('disabled', false);
            } else if (scope == 3) {
                $('#scope_job').show().find('select, input').prop('disabled', false);
            } else if (scope == 4) {
                $('#scope_branch').show().find('select, input').prop('disabled', false);
            }
        }

        function updateTypeFields() {
            var type = $('input[name="type"]:checked').val();

            $('#delay_saudi_rules, #absence_saudi_rules, #overtime_rules').hide();
            $('#calculation_type_div, #salary_effectdiv').hide();

            if (type == 1) { // Absence
                $('#absence_saudi_rules').show();
                // $('#calculation_type_div').show();
                $('#salary_effectdiv').show();
            } else if (type == 2) { // Late
                $('#delay_saudi_rules').show();
                $('#calculation_type_div').show();
                $('#salary_effectdiv').show();
            } else if (type == 3) { // Early Exit
                $('#calculation_type_div').show();
                $('#salary_effectdiv').show();
            } else if (type == 4) { // Overtime
                $('#overtime_rules').show();
                 $('#salary_effectdiv').show();
            }
        }

        // Events
        $(document).on('change', 'input[name="scope"]', function() {
            updateScopeFields();
        });

        $(document).on('change', 'input[name="type"]', function() {
            updateTypeFields();
        });

        // Initialize
        updateScopeFields();
        updateTypeFields();

        // Add smooth scrolling to sections
        $('.form-section').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
    });
</script>
@endpush
