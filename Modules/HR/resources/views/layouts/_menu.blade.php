@canany(['hr.jobs.index', 'hr.departments.index', 'hr.shift_types.index', 'hr.document_types.index',
    'hr.contract_types.index', 'hr.holiday_types.index', 'hr.CalendarEvents.index', 'hr.posts.index', 'hr.attendance.movement',
    'hr.Place.index', 'hr.attendance-policies.index', 'hr.allowances.index', 'hr.deducts.index', 'hr.penalties.index',
    'hr.rewards.index', 'hr.salaries.index', 'hr.payrolls.index', 'hr.holidays.index', 'hr.justifications.index',
    'hr.absentrequests.index', 'hr.advances.index', 'hr.trackers.index', 'hr.employees.index', 'hr.documents.index',
    'hr.contracts.index', 'hr.custodies.index', 'hr.EndService.index', 'hr.Archive.index', 'hr.asset_types.index',
    'hr.assets.index', 'hr.Task.index', 'hr.GroupTask.index', 'hr.settings.edit', 'hr.report_types.index',
    'hr.my-requests.index'])

    @php
        $isHrActive = Route::is('hr.*');
    @endphp

    <div x-data="{ open: {{ $isHrActive ? 'true' : 'false' }} }" class="line-menu-item mb-1">
        <button type="button" 
                @click="open = !open" 
                :class="{ 'active-parent': open || {{ $isHrActive ? 'true' : 'false' }} }"
                class="line-menu-btn">
            <div class="d-flex align-items-center gap-3">
                <div class="line-icon-badge icon-hr">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="line-menu-title">@lang('hr::lang.human_resource')</span>
            </div>
            <i class="fas fa-chevron-down line-menu-arrow" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" x-collapse x-cloak class="line-submenu">
            @can('hr.employees.index')
                <a class="line-sub-item {{ Route::is('hr.employees*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.employees.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_employees.plural')</span>
                </a>
            @endcan

            @can('hr.posts.index')
                <a class="line-sub-item {{ Route::is('hr.posts*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.posts.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_posts.plural')</span>
                </a>
            @endcan

            @can('hr.contracts.index')
                <a class="line-sub-item {{ Route::is('hr.contracts*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.contracts.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_contracts.plural')</span>
                </a>
            @endcan

            @can('hr.documents.index')
                <a class="line-sub-item {{ Route::is('hr.documents*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.documents.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_documents.plural')</span>
                </a>
            @endcan

            @can('hr.custodies.index')
                <a class="line-sub-item {{ Route::is('hr.custodies.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.custodies.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_custodies.plural')</span>
                </a>
            @endcan

            @can('hr.holidays.index')
                <a class="line-sub-item {{ Route::is('hr.holidays.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.holidays.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_holidays.plural')</span>
                </a>
            @endcan

            @can('hr.justifications.index')
                <a class="line-sub-item {{ Route::is('hr.justifications.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.justifications.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_justifications.plural')</span>
                </a>
            @endcan

            @can('hr.advances.index')
                <a class="line-sub-item {{ Route::is('hr.advances.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.advances.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_advances.plural')</span>
                </a>
            @endcan

            @can('hr.absentrequests.index')
                <a class="line-sub-item {{ Route::is('hr.absentrequests.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.absentrequests.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_absentrequest.plural')</span>
                </a>
            @endcan

            @can('hr.attendance.movement')
                <a class="line-sub-item {{ Route::is('hr.attendance.movement') ? 'active-sub' : '' }}"
                    href="{{ route('hr.attendance.movement') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_attendances.attendance_movement')</span>
                </a>
            @endcan

            @can('hr.Place.index')
                <a class="line-sub-item {{ Route::is('hr.Place.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.Place.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_places.plural')</span>
                </a>
            @endcan

            @can('hr.attendance-policies.index')
                <a class="line-sub-item {{ Route::is('hr.attendance-policies.index') ? 'active-sub' : '' }}"
                    href="{{ route('hr.attendance-policies.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_attendance_policies.plural')</span>
                </a>
            @endcan

            @can('hr.payrolls.index')
                <a class="line-sub-item {{ Route::is('hr.payrolls.index') ? 'active-sub' : '' }}"
                    href="{{ route('hr.payrolls.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_payrolls.plural')</span>
                </a>
            @endcan

            @can('hr.salaries.index')
                <a class="line-sub-item {{ Route::is('hr.salaries*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.salaries.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_salaries.plural')</span>
                </a>
            @endcan

            @can('hr.penalties.index')
                <a class="line-sub-item {{ Route::is('hr.penalties.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.penalties.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_penalties.plural')</span>
                </a>
            @endcan

            @can('hr.rewards.index')
                <a class="line-sub-item {{ Route::is('hr.rewards.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.rewards.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_rewards.plural')</span>
                </a>
            @endcan

            @can('hr.Task.index')
                <a class="line-sub-item {{ Route::is('hr.Task.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.Task.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_tasks.plural')</span>
                </a>
            @endcan

            @can('hr.GroupTask.index')
                <a class="line-sub-item {{ Route::is('hr.GroupTask.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.GroupTask.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_GroupTask.plural')</span>
                </a>
            @endcan

            @can('hr.report_types.index')
                <a class="line-sub-item {{ Route::is('hr.report_types*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.report_types.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::menu.reports')</span>
                </a>
            @endcan

            @can('hr.departments.index')
                <a class="line-sub-item {{ Route::is('hr.departments*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.departments.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_departments.plural')</span>
                </a>
            @endcan

            @can('hr.jobs.index')
                <a class="line-sub-item {{ Route::is('hr.jobs*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.jobs.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_jobs.plural')</span>
                </a>
            @endcan

            @can('hr.shift_types.index')
                <a class="line-sub-item {{ Route::is('hr.shift_types*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.shift_types.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_shift_types.plural')</span>
                </a>
            @endcan

            @can('hr.trackers.index')
                <a class="line-sub-item {{ Route::is('hr.trackers.index') ? 'active-sub' : '' }}"
                    href="{{ route('hr.trackers.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_trackers.plural')</span>
                </a>
            @endcan

            @can('hr.settings.edit')
                <a class="line-sub-item {{ Route::is('hr.settings.*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.settings.edit', 1) }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::menu.general_settings')</span>
                </a>
            @endcan

            @can('hr.EndService.index')
                <a class="line-sub-item {{ Route::is('hr.EndService*') ? 'active-sub' : '' }}"
                    href="{{ route('hr.EndService.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_end_service.plural')</span>
                </a>
            @endcan

            @can('hr.Archive.index')
                <a class="line-sub-item {{ Route::is('hr.Archive.index') ? 'active-sub' : '' }}"
                    href="{{ route('hr.Archive.index') }}" wire:navigate>
                    <span class="line-sub-dot"></span>
                    <span>@lang('hr::models/hr_archive.plural')</span>
                </a>
            @endcan
        </div>
    </div>
@endcanany
