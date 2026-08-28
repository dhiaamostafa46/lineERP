<div class="row">
    <!-- General Information -->
    <div class="col-12 mb-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@lang('crud.detail') @lang('hr::models/hr_attendance_policies.singular') </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Main Details -->
                    <div class="col-lg-8">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-4">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.name')</label>
                                <div class="fw-bold fs-6">{{ $policy->name }}</div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-6 mb-4">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.type')</label>
                                <div class="fw-bold fs-6">{{ $policy->type_text }}</div>
                            </div>

                            <!-- Scope -->
                            <div class="col-md-6 mb-4">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.scope')</label>
                                <div class="fw-bold fs-6">{{ $policy->scope_text }}</div>
                            </div>

                            <!-- Calculation Type -->
                            <div class="col-md-6 mb-4">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.calculation_type')</label>
                                <div class="fw-bold fs-6">{{ $policy->calculation_type_text }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.is_automatic')</label>
                                <div class="fw-bold fs-6">{{ $policy->is_automatic_text ?? '-' }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.status')</label>
                                <div class="fw-bold fs-6 {{ $policy->status_badge }}">{{ $policy->status_text ?? '-' }}
                                </div>
                            </div>


                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="fw-bold text-muted">@lang('hr::models/hr_attendance_policies.fields.description')</label>
                                <div class="fw-bold fs-6">{{ $policy->description ?? '-' }}</div>
                            </div>


                        </div>
                    </div>

                    <!-- Status & Meta -->
                    <div class="col-lg-4">
                        <div class="bg-light p-4 rounded border">
                            <!-- Affects Salary -->


                            @if ($policy->SalaryEffectBasic)
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold text-muted">
                                        {{ __('hr::models/hr_salaries.fields.basic') }}</span>
                                    <span>
                                        {{-- <span class="badge {{ $effectTypeBadge }}">{{ $effectTypeText }}</span> --}}
                                    </span>
                                </div>
                            @endif

                            @foreach ($policy->SalaryEffectAllowancesModels as $Allowances)
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold text-muted">{{ $Allowances->name }}</span>
                                    <span>
                                        {{-- <span class="badge {{ $effectTypeBadge }}">{{ $effectTypeText }}</span> --}}
                                    </span>
                                </div>
                            @endforeach

                            <!-- Is Automatic -->
                            <!-- Start Date -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scope Entities Section -->
    <div class="col-12 mb-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                  {{ $policy->scope_text }}
                    <span class="badge badge-primary"></span>
                </h3>
            </div>
            <div class="card-body">
                @if ($policy->scope_entities->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="20%">@lang('hr::models/hr_attendance_policies.fields.id')</th>
                                    <th width="70%">@lang('hr::models/hr_attendance_policies.fields.entity_name')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($policy->scope_entities as $index => $entity)
                                    <tr>


                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge badge-light-info">{{ $entity->id }}</span></td>
                                        <td>{{ $entity->name ?? $entity->username }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>@lang('hr::models/hr_attendance_policies.messages.no_entities')</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Rules Settings -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">@lang('hr::models/hr_attendance_policies.rules_settings')</h3>
            </div>
            <div class="card-body">

                <!-- Delay Rules (TYPE_LATE = 2) -->
                @if ($policy->type == \Modules\HR\App\Models\HrAttendancePolicy::TYPE_LATE && $policy->delay_settings)
                    <div class="card border-warning shadow-sm mb-3">
                        <div class="card-header bg-warning text-dark d-flex align-items-center">
                            <h5 class="mb-0">@lang('hr::models/hr_attendance_policies.saudi_penalties.title')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-circle text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.delay_info_1')</span>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-circle text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.delay_info_2')</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="fas fa-circle text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.delay_info_3')</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h5 class="text-primary mb-3">
                        <i class="fas fa-calendar-day me-2"></i>
                        @lang('hr::models/hr_attendance_policies.saudi_penalties.deduction')
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="align-middle">@lang('hr::models/hr_attendance_policies.saudi_penalties.time_range')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.first')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.second')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.third')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.fourth')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $ranges = [
                                        '0-15 دقيقة' => 'late_15',
                                        '15-30 دقيقة' => 'late_15_30',
                                        '30-60 دقيقة' => 'late_30_60',
                                        'أكثر من 60 دقيقة' => 'late_60_plus',
                                        'خروج مبكر 15 دقيقة' => 'early_exit_15',
                                        'خروج مبكر أكثر من 15 دقيقة' => 'early_exit_15_plus',
                                    ];
                                @endphp
                                @foreach ($policy->delay_settings as $rangeName => $occurrences)
                                    <tr>
                                        <td class="fw-bold">{{ $rangeName }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-light-primary">{{ $occurrences['المرة الأولى'] ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-light-warning">{{ $occurrences['المرة الثانية'] ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-light-danger">{{ $occurrences['المرة الثالثة'] ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-light-dark">{{ $occurrences['المرة الرابعة'] ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Absence Rules (TYPE_ABSENCE = 1) -->
                @if ($policy->type == \Modules\HR\App\Models\HrAttendancePolicy::TYPE_ABSENCE && $policy->absence_settings)
                    <div class="card border-warning shadow-sm mb-3">
                        <div class="card-header bg-warning text-dark d-flex align-items-center">
                            <h5 class="mb-0">@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_title')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-circle text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_info_1')</span>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="fas fa-circle text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_info_2')</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="fas fa-circle text-warning mt-1 me-2" style="font-size: 8px;"></i>
                                    <span>@lang('hr::models/hr_attendance_policies.saudi_penalties.absence_info_3')</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.first')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.second')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.third')</th>
                                    <th class="text-center">@lang('hr::models/hr_attendance_policies.saudi_penalties.recurrence.fourth')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-light-primary fs-6">{{ $policy->absence_settings['المرة الأولى'] ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-light-warning fs-6">{{ $policy->absence_settings['المرة الثانية'] ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-light-danger fs-6">{{ $policy->absence_settings['المرة الثالثة'] ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-light-dark fs-6">{{ $policy->absence_settings['المرة الرابعة'] ?? '-' }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Overtime Rules (TYPE_OVERTIME = 4) -->
                @if ($policy->type == \Modules\HR\App\Models\HrAttendancePolicy::TYPE_OVERTIME)
                    <div class="card border-success shadow-sm mb-3">
                        <div class="card-header bg-success text-white d-flex align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                @lang('hr::models/hr_attendance_policies.rules.overtime_rate')
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="display-4 fw-bold text-success">
                                {{ $policy->settings['overtime_rate'] ?? '0' }}%
                            </div>
                            <p class="text-muted mt-2">@lang('hr::models/hr_attendance_policies.rules.overtime_description')</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
