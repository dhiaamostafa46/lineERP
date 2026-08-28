@canany(['hr.jobs.index', 'hr.departments.index', 'hr.shift_types.index', 'hr.document_types.index',
    'hr.contract_types.index', 'hr.holiday_types.index', 'hr.CalendarEvents.index', 'hr.posts.index', 'hr.attendance.movement',
    'hr.Place.index', 'hr.attendance-policies.index', 'hr.allowances.index', 'hr.deducts.index', 'hr.penalties.index',
    'hr.rewards.index', 'hr.salaries.index', 'hr.payrolls.index', 'hr.holidays.index', 'hr.justifications.index',
    'hr.absentrequests.index', 'hr.advances.index', 'hr.trackers.index', 'hr.employees.index', 'hr.documents.index',
    'hr.contracts.index', 'hr.custodies.index', 'hr.EndService.index', 'hr.Archive.index', 'hr.asset_types.index',
    'hr.assets.index', 'hr.Task.index', 'hr.GroupTask.index', 'hr.settings.edit', 'hr.report_types.index',
    'hr.my-requests.index'])
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ Route::is('hr.*') ? 'here show' : '' }}">
        <span class="menu-link">
            <span class="menu-bullet">
                <i class="ki-duotone ki-profile-user fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </span>
            <span class="menu-title">@lang('hr::lang.human_resource')</span>
            <span class="menu-arrow"></span>
        </span>


        <div class="menu-sub menu-sub-accordion">
		    
			 @can('hr.posts.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.posts*') ? 'active' : '' }}"
                                    href="{{ route('hr.posts.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-bullhorn"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_posts.plural')</span>
                                </a>
                            </div>
                        @endcan
			@can('hr.employees.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('hr.employees*') ? 'active' : '' }}"
                         href="{{ route('hr.employees.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-user-tie"></i>
                        </span>
                        <span class="menu-title">@lang('hr::models/hr_employees.plural')</span>
                    </a>
                </div>
            @endcan
			 @can('hr.contracts.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.contracts*') ? 'active' : '' }}"
                                    href="{{ route('hr.contracts.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-signature"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_contracts.plural')</span>
                                </a>
                            </div>
                        @endcan
			@can('hr.documents.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.documents*') ? 'active' : '' }}"
                                    href="{{ route('hr.documents.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-file-alt"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_documents.plural')</span>
                                </a>
                            </div>
            @endcan
			@can('hr.custodies.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.custodies.*') ? 'active' : '' }}"
                                    href="{{ route('hr.custodies.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-box"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_custodies.plural')</span>
                                </a>
                            </div>
            @endcan

            @can('hr.EndService.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.EndService*') ? 'active' : '' }}"
                                    href="{{ route('hr.EndService.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-user-slash"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_end_service.plural')</span>
                                </a>
                            </div>
            @endcan
            @can('hr.Archive.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.Archive.index') ? 'active' : '' }}"
                                    href="{{ route('hr.Archive.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-archive"></i>
                                    </span>
                                    <span class="menu-title"> @lang('hr::models/hr_archive.plural')</span>
                                </a>
                            </div>
            @endcan
			
			<!----------------------------------------------Start Requests------------------------------------------------------------------------------->
            @canany(['hr.holidays.index', 'hr.absentrequests.index', 'hr.advances.index'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is(['hr.holidays.*', 'hr.justifications.*', 'hr.absentrequests.*', 'hr.advances.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-code-pull-request"></i>

                        </span>
                        <span class="menu-title">@lang('hr::menu.requests')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        @can('hr.holidays.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.holidays.*') ? 'active' : '' }}"
                                    href="{{ route('hr.holidays.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-calendar-times"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_holidays.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.justifications.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.justifications.*') ? 'active' : '' }}"
                                    href="{{ route('hr.justifications.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-comment-dots"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_justifications.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('hr.advances.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.advances.*') ? 'active' : '' }}"
                                    href="{{ route('hr.advances.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_advances.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.absentrequests.index')
                        <div class="menu-item">
                            <a class="menu-link {{ Route::is('hr.absentrequests.*') ? 'active' : '' }}"
                                href="{{ route('hr.absentrequests.index') }}">
                                <span class="menu-bullet">
                                    <i class="nav-icon fas fa-diamond"></i>
                                </span>
                                <span class="menu-title">@lang('hr::models/hr_absentrequest.plural')</span>
                            </a>
                        </div>
                        @endcan 


                    </div>
                </div>
            @endcanany
            <!----------------------------------------------End Requests------------------------------------------------------------------------------->
            <!----------------------------------------------Start Attendance------------------------------------------------------------------------------->
            @canany(['hr.Place.index', 'hr.assets.index'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is(['hr.attendance.*', 'hr.Place.*', 'hr.attendance-policies.*', 'hr.attend.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-clipboard-user"></i>

                        </span>
                        <span class="menu-title">@lang('hr::menu.attendance')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        @can('hr.attendance.movement')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.attendance.movement') ? 'active' : '' }}"
                                    href="{{ route('hr.attendance.movement') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-walking"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_attendances.attendance_movement')</span>
                                </a>
                            </div>
                        @endcan

                        @can('hr.Place.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.Place.*') ? 'active' : '' }}"
                                    href="{{ route('hr.Place.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-map-marker-alt"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_places.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('hr.attendance-policies.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.attendance-policies.index') ? 'active' : '' }}"
                                    href="{{ route('hr.attendance-policies.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-user-shield"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_attendance_policies.plural')</span>
                                </a>
                            </div>
                        @endcan

                        {{-- @can('hr.Place.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.Place.*') ? 'active' : '' }}"
                                    href="{{ route('hr.Place.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-diamond"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_places.plural')</span>
                                </a>
                            </div>
                        @endcan --}}
                        @can('hr.attendance.movement')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.attend.hourscalculate') ? 'active' : '' }}"
                                    href="{{ route('hr.attend.hourscalculate') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-calculator"></i>
                                    </span>
                                    <span class="menu-title">حاسبة خارج الدوام</span>
                                </a>
                            </div>
                        @endcan



                    </div>
                </div>
            @endcanany
            <!----------------------------------------------End Attendance------------------------------------------------------------------------------->
            <!----------------------------------------------Start Tasks------------------------------------------------------------------------------->
            @canany(['hr.Task.index', 'hr.GroupTask.index'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is(['hr.Task.*', 'hr.GroupTask.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-bars-progress"></i>

                        </span>
                        <span class="menu-title">@lang('hr::menu.tasks')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        @can('hr.Task.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.Task.*') ? 'active' : '' }}"
                                    href="{{ route('hr.Task.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-tasks"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_tasks.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.GroupTask.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.GroupTask.*') ? 'active' : '' }}"
                                    href="{{ route('hr.GroupTask.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-users-cog"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_GroupTask.plural')</span>
                                </a>
                            </div>
                        @endcan




                    </div>
                </div>
            @endcanany
            <!----------------------------------------------End Tasks------------------------------------------------------------------------------->
            <!----------------------------------------------Start Salaries------------------------------------------------------------------------------->
            @canany(['hr.salaries.index', 'hr.penalties.index', 'hr.rewards.index',
                'hr.payrolls.index'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is([ 'hr.penalties.*', 'hr.rewards.*', 'hr.salaries.*', 'hr.payrolls.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>

                        </span>
                        <span class="menu-title">@lang('hr::menu.salaries')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                         @can('hr.payrolls.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.payrolls.index') ? 'active' : '' }}"
                                    href="{{ route('hr.payrolls.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-file-invoice"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_payrolls.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.penalties.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.penalties.*') ? 'active' : '' }}"
                                    href="{{ route('hr.penalties.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-gavel"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_penalties.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.rewards.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.rewards.*') ? 'active' : '' }}"
                                    href="{{ route('hr.rewards.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-gift"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_rewards.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.salaries.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.salaries*') ? 'active' : '' }}"
                                    href="{{ route('hr.salaries.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-money-check-alt"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_salaries.plural')</span>
                                </a>
                            </div>
                        @endcan

                       

                    </div>
                </div>
            @endcanany
            <!----------------------------------------------End Salaries------------------------------------------------------------------------------->
            <!----------------------------------------------Reports------------------------------------------------------------------------------------->
            @can('hr.report_types.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('hr.report_types*') ? 'active' : '' }}"
                        href="{{ route('hr.report_types.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-list-check"></i>
                        </span>
                        <span class="menu-title">@lang('hr::menu.reports')</span>
                    </a>
                </div>
            @endcan
            <!----------------------------------------------End Reports------------------------------------------------------------------------------------->
	
			<!----------------------------------------------settings------------------------------------------------------------------------------------->
			    @canany(['hr.holidays.index', 'hr.absentrequests.index', 'hr.advances.index', 'hr.trackers.index'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is(['hr.holidays.*', 'hr.justifications.*', 'hr.absentrequests.*', 'hr.advances.*', 'hr.trackers.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-gear"></i>

                        </span>
                        <span class="menu-title">@lang('hr::menu.settings')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        @can('hr.settings.edit')
                         <div class="menu-item">
                            <a class="menu-link {{ Route::is('hr.settings.*') ? 'active' : '' }}"
                             href="{{ route('hr.settings.edit', 1) }}">
                            <span class="menu-bullet">
                              <i class="nav-icon fas fa-gear"></i>
                            </span>
                             <span class="menu-title">@lang('hr::menu.general_settings')</span>
                            </a>
                         </div>
                        @endcan
						@can('hr.shift_types.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.shift_types*') ? 'active' : '' }}"
                                    href="{{ route('hr.shift_types.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-user-clock"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_shift_types.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.trackers.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.trackers.index') ? 'active' : '' }}"
                                    href="{{ route('hr.trackers.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-history"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_trackers.plural')</span>
                                </a>
                            </div>
                        @endcan
						@can('hr.jobs.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.jobs*') ? 'active' : '' }}"
                                    href="{{ route('hr.jobs.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-briefcase"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_jobs.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('hr.departments.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.departments*') ? 'active' : '' }}"
                                    href="{{ route('hr.departments.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-sitemap"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_departments.plural')</span>
                                </a>
                            </div>
                        @endcan

                        
                        @can('hr.document_types.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.document_types*') ? 'active' : '' }}"
                                    href="{{ route('hr.document_types.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-id-card"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_document_types.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.contract_types.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.contract_types*') ? 'active' : '' }}"
                                    href="{{ route('hr.contract_types.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-file-contract"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_contract_types.plural')</span>
                                </a>
                            </div>
                        @endcan
						 @can('hr.holiday_types.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.holiday_types*') ? 'active' : '' }}"
                                    href="{{ route('hr.holiday_types.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-umbrella-beach"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_holiday_types.plural')</span>
                                </a>
                            </div>
                        @endcan
						 @can('hr.allowances.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.allowances*') ? 'active' : '' }}"
                                    href="{{ route('hr.allowances.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-plus-circle"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_allowances.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('hr.deducts.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.deducts*') ? 'active' : '' }}"
                                    href="{{ route('hr.deducts.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-minus-circle"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_deducts.plural')</span>
                                </a>
                            </div>
                        @endcan
                        @can('hr.CalendarEvents.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.CalendarEvents*') ? 'active' : '' }}"
                                    href="{{ route('hr.CalendarEvents.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-calendar-alt"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_calendar_events.plural')</span>
                                </a>
                            </div>
                        @endcan
						<!----------------------------------------------Start Asset------------------------------------------------------------------------------->
            @canany(['hr.asset_types.index', 'hr.assets.index'])
                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion {{ Route::is(['hr.asset_types.*', 'hr.assets.*']) ? 'here show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-landmark-flag"></i>

                        </span>
                        <span class="menu-title">@lang('hr::menu.assets')</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        @can('hr.asset_types.index')
                            <div class="menu-item">
                                <a class="menu-link {{ Route::is('hr.asset_types*') ? 'active' : '' }}"
                                    href="{{ route('hr.asset_types.index') }}">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-cubes"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_asset_types.plural')</span>
                                </a>
                            </div>
                        @endcan

                        @can('hr.assets.index')
                            <div class="menu-item active">
                                <a class="menu-link {{ Route::is('hr.assets.*') ? 'active' : '' }}"
                                    href="{{ route('hr.assets.index') }}" previewlistener="true">
                                    <span class="menu-bullet">
                                        <i class="nav-icon fas fa-laptop"></i>
                                    </span>
                                    <span class="menu-title">@lang('hr::models/hr_assets.plural')</span>
                                </a>
                            </div>
                        @endcan




                    </div>
                </div>
            @endcanany
            <!----------------------------------------------End Asset------------------------------------------------------------------------------->

						


                    </div>
                </div>
            @endcanany
			<!------------------------------------------------------end settings------------------------------------------------------------------------------>
			
			
            


            @can('hr.my-requests.index')
                <div class="menu-item">
                    <a class="menu-link {{ Route::is('hr.empdashboard.index') ? 'active' : '' }}"
                        href="{{ route('hr.empdashboard.index') }}">
                        <span class="menu-bullet">
                            <i class="nav-icon fas fa-address-card"></i>
                        </span>
                        <span class="menu-title">الملف الشخصي</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>
@endcanany
