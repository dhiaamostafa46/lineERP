@php
    $isEmployeeRole = !empty($role) && in_array($role->name, ['موظف', 'employee']);

    // Define Module Groups
    $moduleDefinitions = [
        'hr' => [
            'title' => __('permission.modules.hr') ?: 'الموارد البشرية',
            'icon' => 'fas fa-users-cog',
            'prefixes' => ['hr_'],
        ],
        'accusoft' => [
            'title' => __('permission.modules.accounting') ?: 'المحاسبة والمالية',
            'icon' => 'fas fa-calculator',
            'prefixes' => ['accusoft_', 'fnc_', 'accusoft'],
        ],
         'pos' => [
            'title' => __('permission.modules.pos') ?: 'نقاط البيع',
            'icon' => 'fas fa-file-invoice-dollar',
            'prefixes' => ['pos_'],
        ],
        'invoices' => [
            'title' => __('permission.modules.invoices') ?: 'المبيعات والمشتريات',
            'icon' => 'fas fa-file-invoice-dollar',
            'prefixes' => ['invoices_'],
        ],
        'store' => [
            'title' => __('permission.modules.store') ?: 'المخازن والمستودعات',
            'icon' => 'fas fa-boxes',
            'prefixes' => ['store_'],
        ],
        'basicdata' => [
            'title' => __('permission.modules.basic_data') ?: 'البيانات الأساسية',
            'icon' => 'fas fa-database',
            'prefixes' => ['basicdata_'],
        ],
    ];

    $groupedPermissions = [];
    foreach ($moduleDefinitions as $key => $def) {
        $groupedPermissions[$key] = ['permissions' => [], 'total_count' => 0, 'selected_count' => 0];
    }
    $groupedPermissions['general'] = ['permissions' => [], 'total_count' => 0, 'selected_count' => 0];

    $selectedIds = $permissions_selected ?? [];


    // dd($permissions);
    foreach ($permissions as $model => $actions) {
        $found = false;
        $groupTotal = count($actions);
        $groupSelected = 0;
        foreach ($actions as $p) {
            if (in_array($p->id, $selectedIds)) {
                $groupSelected++;
            }
        }

        foreach ($moduleDefinitions as $key => $def) {
            foreach ($def['prefixes'] as $prefix) {
                if (str_starts_with($model, $prefix)) {
                    $groupedPermissions[$key]['permissions'][$model] = $actions;
                    $groupedPermissions[$key]['total_count'] += $groupTotal;
                    $groupedPermissions[$key]['selected_count'] += $groupSelected;
                    $found = true;
                    break 2;
                }
            }
        }
        if (!$found) {
            $groupedPermissions['general']['permissions'][$model] = $actions;
            $groupedPermissions['general']['total_count'] += $groupTotal;
            $groupedPermissions['general']['selected_count'] += $groupSelected;
        }
    }

    $groupedPermissions = array_filter($groupedPermissions, function ($group) {
        return count($group['permissions']) > 0;
    });
@endphp

<style>
    .permissions-sidebar {
        border-inline-end: 1px solid #eff2f5;
        padding: 2rem 1.5rem !important;
        background: #fcfcfc;
    }

    .side-nav-link {
        display: flex;
        align-items: center;
        padding: 0.85rem 1rem;
        color: #5e6278;
        font-weight: 600;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        width: 100%;
        text-align: start;
    }

    .side-nav-link.active {
        background-color: #6A669D;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(106, 102, 157, 0.2);
    }

    .side-nav-link i {
        font-size: 1.1rem;
        margin-inline-end: 0.75rem;
        width: 24px;
        text-align: center;
    }

    .side-nav-link.active i {
        color: #ffffff;
    }

    .permission-section {
        padding: 1.5rem 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .permission-section:last-child {
        border-bottom: none;
    }

    .permission-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .permission-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #181c32;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .permission-section-title::before {
        content: "";
        width: 4px;
        height: 18px;
        background: #6A669D;
        border-radius: 4px;
    }

    .module-header-flat {
        padding: 1.5rem 2rem;
        background: #ffffff;
        border-bottom: 2px solid #f8f9fa;
        margin-bottom: 1rem;
        position: sticky;
        top: 0;

    }

    .count-badge-flat {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        background: rgba(0, 0, 0, 0.05);
    }

    .side-nav-link.active .count-badge-flat {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .perm-item-wrapper:hover {
        background: #f9f9f9;
    }

    /* Dark Mode Support */
    [data-bs-theme="dark"] .permissions-sidebar {
        background: #182234 !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .side-nav-link {
        color: #94a3b8 !important;
    }

    [data-bs-theme="dark"] .side-nav-link.active {
        background-color: #1B325B !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .permission-section {
        border-bottom-color: #334155 !important;
    }

    [data-bs-theme="dark"] .permission-section-title {
        color: #f1f5f9 !important;
    }

    [data-bs-theme="dark"] .module-header-flat {
        background: #1e293b !important;
        border-bottom-color: #334155 !important;
    }

    [data-bs-theme="dark"] .count-badge-flat {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .perm-item-wrapper:hover {
        background: #182234 !important;
    }

    [data-bs-theme="dark"] .permissions-sidebar input.form-control {
        background-color: #151e2e !important;
        color: #f8fafc !important;
    }
</style>

<div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 16px;">
    <div class="card-body p-0">
        <div class="row g-0">
            <!-- Left Sidebar -->
            <div class="col-lg-3 permissions-sidebar">
                <div class="mb-10 px-2">
                    <label
                        class="form-label fw-bold text-gray-400 fs-8 text-uppercase ls-1 mb-4">@lang('models/roles.fields.name')</label>
                    {!! Form::text('name', null, [
                        'class' => 'form-control form-control-solid border-0 fs-6 fw-bold',
                        'readonly' => $isEmployeeRole,
                        'style' => 'background-color: #f1f4f9',
                        'placeholder' => __('models/roles.fields.name'),
                    ]) !!}
                </div>

                <div class="d-flex align-items-center justify-content-between mb-6 px-2">
                    <span class="fw-bold text-gray-800">@lang('lang.modules')</span>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input h-20px w-35px" type="checkbox" id="selectAllGlobal">
                    </div>
                </div>

                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                    @foreach ($groupedPermissions as $key => $group)
                        <button class="side-nav-link {{ $loop->first ? 'active' : '' }}"
                            id="v-pills-{{ $key }}-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-{{ $key }}" type="button" role="tab">
                            <i class="{{ $moduleDefinitions[$key]['icon'] ?? 'fas fa-th-large' }}"></i>
                            <span
                                class="flex-grow-1">{{ $key === 'general' ? (__('lang.general_permissions') ?: 'صلاحيات عامة') : $moduleDefinitions[$key]['title'] }}</span>
                            <span class="count-badge-flat" id="badge-{{ $key }}">
                                <span class="selected">{{ $group['selected_count'] }}</span>/<span
                                    class="total">{{ $group['total_count'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-lg-9 bg-white">
                <div class="tab-content" id="v-pills-tabContent">
                    @foreach ($groupedPermissions as $key => $group)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="v-pills-{{ $key }}" role="tabpanel">

                            <div class="module-header-flat d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bolder text-dark m-0">
                                        {{ $key === 'general' ? (__('lang.general_permissions') ?: 'صلاحيات عامة') : $moduleDefinitions[$key]['title'] }}
                                    </h3>
                                    <span class="text-muted fs-7">@lang('lang.manage_module_permissions_desc')</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-gray-600 fw-bold fs-7">@lang('lang.select_all_in_module')</span>
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input h-25px w-45px module-select-all" type="checkbox"
                                            id="mod-all-{{ $key }}" data-module="{{ $key }}">
                                    </div>
                                </div>
                            </div>

                            <div class="px-10 pb-10">
                                @foreach ($group['permissions'] as $model => $actions)
                                    <div class="permission-section">
                                        <div class="permission-section-header">
                                            <div class="permission-section-title">@lang('permission.group.' . $model)</div>
                                            <div class="form-check form-check-custom form-check-sm">
                                                <input class="form-check-input group-select-all" type="checkbox"
                                                    data-group="{{ $model }}"
                                                    data-module="{{ $key }}">
                                            </div>
                                        </div>
                                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-2">
                                            @foreach ($actions as $permission)
                                                <div class="col">
                                                    <div class="perm-item-wrapper d-flex align-items-center">
                                                        <div
                                                            class="form-check form-check-custom form-check-solid form-check-sm">
                                                            <input class="form-check-input permission-checkbox"
                                                                type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                id="p-{{ $permission->id }}"
                                                                data-group="{{ $model }}"
                                                                data-module="{{ $key }}"
                                                                {{ in_array($permission->id, $selectedIds) ? 'checked' : '' }}>
                                                            <label
                                                                class="form-check-label text-gray-700 fw-semibold fs-7 ms-2"
                                                                for="p-{{ $permission->id }}">
                                                                @lang('permission.roles.' . $permission->action)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllGlobal = document.getElementById('selectAllGlobal');
        const moduleSelectAlls = document.querySelectorAll('.module-select-all');
        const groupSelectAlls = document.querySelectorAll('.group-select-all');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        const updateCounters = (moduleKey) => {
            const moduleCheckboxes = document.querySelectorAll(
                `.permission-checkbox[data-module="${moduleKey}"]`);
            const selected = Array.from(moduleCheckboxes).filter(cb => cb.checked).length;
            const badge = document.getElementById(`badge-${moduleKey}`);
            if (badge) {
                badge.querySelector('.selected').textContent = selected;
            }
        };

        const updateModuleSelectAll = (moduleKey) => {
            const moduleCheckboxes = document.querySelectorAll(
                `.permission-checkbox[data-module="${moduleKey}"]`);
            const moduleSelectAll = document.querySelector(
            `.module-select-all[data-module="${moduleKey}"]`);
            if (moduleSelectAll && moduleCheckboxes.length > 0) {
                moduleSelectAll.checked = Array.from(moduleCheckboxes).every(cb => cb.checked);
            }
            updateCounters(moduleKey);
        };

        const updateGroupSelectAll = (groupName) => {
            const groupCheckboxes = document.querySelectorAll(
                `.permission-checkbox[data-group="${groupName}"]`);
            const groupSelectAll = document.querySelector(`.group-select-all[data-group="${groupName}"]`);
            if (groupSelectAll && groupCheckboxes.length > 0) {
                groupSelectAll.checked = Array.from(groupCheckboxes).every(cb => cb.checked);
            }
        };

        const updateGlobalSelectAll = () => {
            if (selectAllGlobal) {
                selectAllGlobal.checked = permissionCheckboxes.length > 0 && Array.from(
                    permissionCheckboxes).every(cb => cb.checked);
            }
        };

        if (selectAllGlobal) {
            selectAllGlobal.addEventListener('change', function() {
                const isChecked = this.checked;
                permissionCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                    updateGroupSelectAll(cb.dataset.group);
                    updateModuleSelectAll(cb.dataset.module);
                });
                groupSelectAlls.forEach(cb => cb.checked = isChecked);
                moduleSelectAlls.forEach(cb => cb.checked = isChecked);
            });
        }

        moduleSelectAlls.forEach(modCb => {
            modCb.addEventListener('change', function() {
                const mod = this.dataset.module;
                const isChecked = this.checked;
                document.querySelectorAll(`.permission-checkbox[data-module="${mod}"]`).forEach(
                    cb => {
                        cb.checked = isChecked;
                        updateGroupSelectAll(cb.dataset.group);
                    });
                document.querySelectorAll(`.group-select-all[data-module="${mod}"]`).forEach(
                    cb => cb.checked = isChecked);
                updateCounters(mod);
                updateGlobalSelectAll();
            });
        });

        groupSelectAlls.forEach(groupCb => {
            groupCb.addEventListener('change', function() {
                const group = this.dataset.group;
                const mod = this.dataset.module;
                const isChecked = this.checked;
                document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`)
                    .forEach(cb => cb.checked = isChecked);
                updateModuleSelectAll(mod);
                updateGlobalSelectAll();
            });
        });

        permissionCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateGroupSelectAll(this.dataset.group);
                updateModuleSelectAll(this.dataset.module);
                updateGlobalSelectAll();
            });
        });

        const mods = new Set(Array.from(permissionCheckboxes).map(cb => cb.dataset.module));
        mods.forEach(mod => updateModuleSelectAll(mod));
        const groups = new Set(Array.from(permissionCheckboxes).map(cb => cb.dataset.group));
        groups.forEach(group => updateGroupSelectAll(group));
        updateGlobalSelectAll();

        // ✅ معالجة مشكلة max_input_vars عند إرسال النموذج (1146 صلاحية تتجاوز الحد الأقصى 1000)
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                let hiddenInput = document.getElementById('hidden-permissions-input');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'permissions';
                    hiddenInput.id = 'hidden-permissions-input';
                    form.appendChild(hiddenInput);
                }
                
                // جمع قيم الصلاحيات المحددة فقط
                const selectedPerms = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => cb.value);
                hiddenInput.value = JSON.stringify(selectedPerms);
                
                // إزالة خاصية name من المربعات الأصلية لكي لا يتم إرسالها وتخطي الحد الأقصى
                document.querySelectorAll('.permission-checkbox').forEach(cb => cb.removeAttribute('name'));
            });
        }
    });
</script>
