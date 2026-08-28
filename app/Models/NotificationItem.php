<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\NotificationLog\Models\NotificationLogItem;

class NotificationItem extends NotificationLogItem
{
    use HasFactory;

    public $table = 'notification_log_items';

    // 🔵 Notification Status Constants
    const STATUS_PENDING = 1;

    const STATUS_READ = 2;

    const STATUS_CONFIRMED = 3;

    const STATUS_CANCELLED = 4;

    // 🔥 Priority Constants
    const PRIORITY_LOW = 1;

    const PRIORITY_NORMAL = 2;

    const PRIORITY_HIGH = 3;

    const PRIORITY_URGENT = 4;

    // 🟣 System Module Constants
    const MODULE_HR = 'hr';

    const MODULE_VEHICLES = 'vehicles';

    const MODULE_INVOICES = 'invoices';

    const MODULE_STORE = 'store';

    const MODULE_POS = 'pos';

    const MODULE_ACCOUNTING = 'accounting';

    const MODULE_ASSETS = 'assets';

    const MODULE_SYSTEM = 'system';

    // 🔵 HR Notification Types
    const TYPE_IQAMA_EXPIRY = 'iqama_expiry';

    const TYPE_INSURANCE_EXPIRY = 'insurance_expiry';

    const TYPE_PASSPORT_EXPIRY = 'passport_expiry';

    const TYPE_LEAVE_REQUEST = 'leave_request';

    const TYPE_ADVANCE_REQUEST = 'advance_request';

    const TYPE_SETTLEMENT_REQUEST = 'settlement_request';

    const TYPE_REQUEST_STATUS = 'request_status';

    // 🚚 Vehicles & Maintenance Notification Types
    const TYPE_VEHICLE_LICENSE_EXPIRY = 'vehicle_license_expiry';

    const TYPE_DRIVER_LICENSE_EXPIRY = 'driver_license_expiry';

    const TYPE_MAINTENANCE_REQUEST = 'maintenance_request';

    const TYPE_TRAFFIC_VIOLATION = 'traffic_violation';

    // 🧾 Invoices & Sales Notification Types
    const TYPE_QUOTATION_EXPIRED = 'quotation_expired';

    const TYPE_INVOICE_DUE = 'invoice_due';

    const TYPE_PURCHASE_RETURN_PENDING = 'purchase_return_pending';

    // 📦 Inventory & Store Notification Types
    const TYPE_LOW_STOCK = 'low_stock';

    const TYPE_STOCK_TRANSFER_PENDING = 'stock_transfer_pending';

    // 💳 POS Notification Types
    const TYPE_POS_SESSION_OPEN = 'pos_session_open';

    const TYPE_POS_CASH_DISCREPANCY = 'pos_cash_discrepancy';

    // 📊 Accounting Notification Types
    const TYPE_UNPOSTED_JOURNAL_ENTRY = 'unposted_journal_entry';

    // 🏛️ Fixed Assets Notification Types
    const TYPE_ASSET_MAINTENANCE = 'asset_maintenance';

    const TYPE_ASSET_DEPRECIATION = 'asset_depreciation';

    // ⚙️ System Notification Types
    const TYPE_SYSTEM_ALERT = 'system_alert';

    // 🔵 Notification Channel Constants
    const CHANNEL_DATABASE = 'database';

    const CHANNEL_EMAIL = 'email';

    const CHANNEL_SMS = 'sms';

    const CHANNEL_MOBILE_PUSH = 'mobile_push';

    protected $fillable = [
        'org_id',
        'user_id',
        'target_permission',
        'target_role',
        'notification_type',
        'notifiable_id',
        'notifiable_type',
        'title',
        'body',
        'channel',
        'status',
        'priority',
        'fingerprint',
        'read_at',
        'extra',
        'anonymous_notifiable_properties',
        'confirmed_at',
    ];

    protected $casts = [
        'extra' => 'array',
        'anonymous_notifiable_properties' => 'array',
        'read_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'priority' => 'integer',
    ];

    public static function priorities(): array
    {
        return [
            self::PRIORITY_LOW => __('models/notifications.priority.low'),
            self::PRIORITY_NORMAL => __('models/notifications.priority.normal'),
            self::PRIORITY_HIGH => __('models/notifications.priority.high'),
            self::PRIORITY_URGENT => __('models/notifications.priority.urgent'),
        ];
    }

    public function getPriorityNameAttribute(): string
    {
        return self::priorities()[$this->priority] ?? __('models/notifications.priority.normal');
    }

    public function getPriorityColorAttribute(): string
    {
        return match ((int) $this->priority) {
            self::PRIORITY_URGENT => 'danger',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_NORMAL => 'primary',
            self::PRIORITY_LOW => 'secondary',
            default => 'primary',
        };
    }

    public static function modules(): array
    {
        return [
            self::MODULE_HR => __('models/notifications.modules.hr'),
            self::MODULE_VEHICLES => __('models/notifications.modules.vehicles'),
            self::MODULE_INVOICES => __('models/notifications.modules.invoices'),
            self::MODULE_STORE => __('models/notifications.modules.store'),
            self::MODULE_POS => __('models/notifications.modules.pos'),
            self::MODULE_ACCOUNTING => __('models/notifications.modules.accounting'),
            self::MODULE_ASSETS => __('models/notifications.modules.assets'),
            self::MODULE_SYSTEM => __('models/notifications.modules.system'),
        ];
    }

    public static function modulePermissions(): array
    {
        return [
            self::MODULE_HR => ['hr.documents.index', 'hr.holidays.index', 'hr.advances.index', 'hr.justifications.index', 'hr.employees.index'],
            self::MODULE_VEHICLES => ['vc.vehicles.index', 'vc.maintenance_requests.index'],
            self::MODULE_INVOICES => ['invoices.sales.index', 'invoices.quotations.index', 'invoices.purchase_return.index', 'invoices.purchase.index'],
            self::MODULE_STORE => ['store.stores.index', 'store.direct_transfer.index', 'store.receiving.index', 'store.issuing.index'],
            self::MODULE_POS => ['pos.index'],
            self::MODULE_ACCOUNTING => ['accusoft.JournalEntry.index', 'accusoft.TreeAccounts.index'],
            self::MODULE_ASSETS => ['accusoft.assets.index'],
            self::MODULE_SYSTEM => [],
        ];
    }

    public static function userModules(?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $allModules = self::modules();

        if (! $user) {
            return [];
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('owner')) {
            return $allModules;
        }

        $permittedModules = [];
        $modulePerms = self::modulePermissions();

        foreach ($allModules as $modKey => $label) {
            $perms = $modulePerms[$modKey] ?? [];

            if (empty($perms) || $modKey === self::MODULE_SYSTEM) {
                $permittedModules[$modKey] = $label;

                continue;
            }

            if (method_exists($user, 'can')) {
                foreach ($perms as $perm) {
                    if ($user->can($perm)) {
                        $permittedModules[$modKey] = $label;

                        break;
                    }
                }
            }
        }

        return $permittedModules;
    }

    public static function moduleIcons(): array
    {
        return [
            self::MODULE_HR => 'fa-users-gear',
            self::MODULE_VEHICLES => 'fa-truck-pickup',
            self::MODULE_INVOICES => 'fa-file-invoice-dollar',
            self::MODULE_STORE => 'fa-boxes-stacked',
            self::MODULE_POS => 'fa-cash-register',
            self::MODULE_ACCOUNTING => 'fa-calculator',
            self::MODULE_ASSETS => 'fa-building-circle-check',
            self::MODULE_SYSTEM => 'fa-bell',
        ];
    }

    public static function typeModuleMap(): array
    {
        return [
            self::TYPE_IQAMA_EXPIRY => self::MODULE_HR,
            self::TYPE_INSURANCE_EXPIRY => self::MODULE_HR,
            self::TYPE_PASSPORT_EXPIRY => self::MODULE_HR,
            self::TYPE_LEAVE_REQUEST => self::MODULE_HR,
            self::TYPE_ADVANCE_REQUEST => self::MODULE_HR,
            self::TYPE_SETTLEMENT_REQUEST => self::MODULE_HR,
            self::TYPE_REQUEST_STATUS => self::MODULE_HR,

            self::TYPE_VEHICLE_LICENSE_EXPIRY => self::MODULE_VEHICLES,
            self::TYPE_DRIVER_LICENSE_EXPIRY => self::MODULE_VEHICLES,
            self::TYPE_MAINTENANCE_REQUEST => self::MODULE_VEHICLES,
            self::TYPE_TRAFFIC_VIOLATION => self::MODULE_VEHICLES,

            self::TYPE_QUOTATION_EXPIRED => self::MODULE_INVOICES,
            self::TYPE_INVOICE_DUE => self::MODULE_INVOICES,
            self::TYPE_PURCHASE_RETURN_PENDING => self::MODULE_INVOICES,

            self::TYPE_LOW_STOCK => self::MODULE_STORE,
            self::TYPE_STOCK_TRANSFER_PENDING => self::MODULE_STORE,

            self::TYPE_POS_SESSION_OPEN => self::MODULE_POS,
            self::TYPE_POS_CASH_DISCREPANCY => self::MODULE_POS,

            self::TYPE_UNPOSTED_JOURNAL_ENTRY => self::MODULE_ACCOUNTING,

            self::TYPE_ASSET_MAINTENANCE => self::MODULE_ASSETS,
            self::TYPE_ASSET_DEPRECIATION => self::MODULE_ASSETS,

            self::TYPE_SYSTEM_ALERT => self::MODULE_SYSTEM,
        ];
    }

    public static function typePermissions(): array
    {
        return [
            // HR — الموارد البشرية
            self::TYPE_IQAMA_EXPIRY => 'hr.documents.index',
            self::TYPE_INSURANCE_EXPIRY => 'hr.documents.index',
            self::TYPE_PASSPORT_EXPIRY => 'hr.documents.index',
            self::TYPE_LEAVE_REQUEST => 'hr.holidays.index',
            self::TYPE_ADVANCE_REQUEST => 'hr.advances.index',
            self::TYPE_SETTLEMENT_REQUEST => 'hr.justifications.index',
            self::TYPE_REQUEST_STATUS => null, // موجهة للموظف نفسه دائماً

            // المركبات
            self::TYPE_VEHICLE_LICENSE_EXPIRY => 'vc.vehicles.index',
            self::TYPE_DRIVER_LICENSE_EXPIRY => 'vc.vehicles.index',
            self::TYPE_MAINTENANCE_REQUEST => 'vc.maintenance_requests.index',
            self::TYPE_TRAFFIC_VIOLATION => 'vc.vehicles.index',

            // الفواتير والمبيعات
            self::TYPE_QUOTATION_EXPIRED => 'invoices.quotations.index',
            self::TYPE_INVOICE_DUE => 'invoices.sales.index',
            self::TYPE_PURCHASE_RETURN_PENDING => 'invoices.purchase_return.index',

            // المخزون
            self::TYPE_LOW_STOCK => 'store.stores.index',
            self::TYPE_STOCK_TRANSFER_PENDING => 'store.direct_transfer.index',

            // نقاط البيع
            self::TYPE_POS_SESSION_OPEN => 'pos.index',
            self::TYPE_POS_CASH_DISCREPANCY => 'pos.index',

            // المحاسبة
            self::TYPE_UNPOSTED_JOURNAL_ENTRY => 'accusoft.JournalEntry.index',

            // الأصول الثابتة
            self::TYPE_ASSET_MAINTENANCE => 'accusoft.assets.index',
            self::TYPE_ASSET_DEPRECIATION => 'accusoft.assets.index',

            // النظام
            self::TYPE_SYSTEM_ALERT => null,
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_IQAMA_EXPIRY => __('models/notifications.type.iqama_expiry'),
            self::TYPE_INSURANCE_EXPIRY => __('models/notifications.type.insurance_expiry'),
            self::TYPE_PASSPORT_EXPIRY => __('models/notifications.type.passport_expiry'),
            self::TYPE_LEAVE_REQUEST => __('models/notifications.type.leave_request'),
            self::TYPE_ADVANCE_REQUEST => __('models/notifications.type.advance_request'),
            self::TYPE_SETTLEMENT_REQUEST => __('models/notifications.type.settlement_request'),
            self::TYPE_REQUEST_STATUS => __('models/notifications.type.request_status'),

            self::TYPE_VEHICLE_LICENSE_EXPIRY => __('models/notifications.type.vehicle_license_expiry'),
            self::TYPE_DRIVER_LICENSE_EXPIRY => __('models/notifications.type.driver_license_expiry'),
            self::TYPE_MAINTENANCE_REQUEST => __('models/notifications.type.maintenance_request'),
            self::TYPE_TRAFFIC_VIOLATION => __('models/notifications.type.traffic_violation'),

            self::TYPE_QUOTATION_EXPIRED => __('models/notifications.type.quotation_expired'),
            self::TYPE_INVOICE_DUE => __('models/notifications.type.invoice_due'),
            self::TYPE_PURCHASE_RETURN_PENDING => __('models/notifications.type.purchase_return_pending'),

            self::TYPE_LOW_STOCK => __('models/notifications.type.low_stock'),
            self::TYPE_STOCK_TRANSFER_PENDING => __('models/notifications.type.stock_transfer_pending'),

            self::TYPE_POS_SESSION_OPEN => __('models/notifications.type.pos_session_open'),
            self::TYPE_POS_CASH_DISCREPANCY => __('models/notifications.type.pos_cash_discrepancy'),

            self::TYPE_UNPOSTED_JOURNAL_ENTRY => __('models/notifications.type.unposted_journal_entry'),

            self::TYPE_ASSET_MAINTENANCE => __('models/notifications.type.asset_maintenance'),
            self::TYPE_ASSET_DEPRECIATION => __('models/notifications.type.asset_depreciation'),

            self::TYPE_SYSTEM_ALERT => __('models/notifications.type.system_alert'),
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => __('models/notifications.status.pending'),
            self::STATUS_READ => __('models/notifications.status.read'),
            self::STATUS_CONFIRMED => __('models/notifications.status.confirmed'),
            self::STATUS_CANCELLED => __('models/notifications.status.cancelled'),
        ];
    }

    public static function channels(): array
    {
        return [
            self::CHANNEL_DATABASE => __('models/notifications.channel.database'),
            self::CHANNEL_EMAIL => __('models/notifications.channel.email'),
            self::CHANNEL_SMS => __('models/notifications.channel.sms'),
            self::CHANNEL_MOBILE_PUSH => __('models/notifications.channel.mobile_push'),
        ];
    }

    public function getChannelNameAttribute(): string
    {
        return self::channels()[$this->channel] ?? (string) $this->channel;
    }

    public function getStatusNameAttribute(): string
    {
        return self::statuses()[$this->status] ?? (string) $this->status;
    }

    public function getTypeNameAttribute(): string
    {
        return self::types()[$this->notification_type] ?? (string) $this->notification_type;
    }

    public function getModuleNameAttribute(): string
    {
        $moduleKey = self::typeModuleMap()[$this->notification_type] ?? self::MODULE_SYSTEM;

        return self::modules()[$moduleKey] ?? $moduleKey;
    }

    public function getModuleKeyAttribute(): string
    {
        return self::typeModuleMap()[$this->notification_type] ?? self::MODULE_SYSTEM;
    }

    public function getIconAttribute(): string
    {
        $icons = [
            self::TYPE_IQAMA_EXPIRY => 'ki-user-square',
            self::TYPE_INSURANCE_EXPIRY => 'ki-shield-tick',
            self::TYPE_PASSPORT_EXPIRY => 'ki-verify',
            self::TYPE_LEAVE_REQUEST => 'ki-calendar-8',
            self::TYPE_ADVANCE_REQUEST => 'ki-wallet',
            self::TYPE_SETTLEMENT_REQUEST => 'ki-calculator',
            self::TYPE_REQUEST_STATUS => 'ki-notification-status',

            self::TYPE_VEHICLE_LICENSE_EXPIRY => 'ki-car-2',
            self::TYPE_DRIVER_LICENSE_EXPIRY => 'ki-user-tick',
            self::TYPE_MAINTENANCE_REQUEST => 'ki-setting-2',
            self::TYPE_TRAFFIC_VIOLATION => 'ki-warning-2',

            self::TYPE_QUOTATION_EXPIRED => 'ki-document',
            self::TYPE_INVOICE_DUE => 'ki-bill',
            self::TYPE_PURCHASE_RETURN_PENDING => 'ki-arrow-two-way',

            self::TYPE_LOW_STOCK => 'ki-package',
            self::TYPE_STOCK_TRANSFER_PENDING => 'ki-delivery-3',

            self::TYPE_POS_SESSION_OPEN => 'ki-shop',
            self::TYPE_POS_CASH_DISCREPANCY => 'ki-dollar',

            self::TYPE_UNPOSTED_JOURNAL_ENTRY => 'ki-book-open',

            self::TYPE_ASSET_MAINTENANCE => 'ki-city',
            self::TYPE_ASSET_DEPRECIATION => 'ki-chart-line-down',

            self::TYPE_SYSTEM_ALERT => 'ki-notification-on',
        ];

        return $icons[$this->notification_type] ?? 'ki-notification-on';
    }

    public function getColorAttribute(): string
    {
        $colors = [
            self::TYPE_IQAMA_EXPIRY => 'danger',
            self::TYPE_INSURANCE_EXPIRY => 'warning',
            self::TYPE_PASSPORT_EXPIRY => 'danger',
            self::TYPE_LEAVE_REQUEST => 'primary',
            self::TYPE_ADVANCE_REQUEST => 'success',
            self::TYPE_SETTLEMENT_REQUEST => 'info',
            self::TYPE_REQUEST_STATUS => 'secondary',

            self::TYPE_VEHICLE_LICENSE_EXPIRY => 'danger',
            self::TYPE_DRIVER_LICENSE_EXPIRY => 'warning',
            self::TYPE_MAINTENANCE_REQUEST => 'primary',
            self::TYPE_TRAFFIC_VIOLATION => 'danger',

            self::TYPE_QUOTATION_EXPIRED => 'warning',
            self::TYPE_INVOICE_DUE => 'danger',
            self::TYPE_PURCHASE_RETURN_PENDING => 'info',

            self::TYPE_LOW_STOCK => 'danger',
            self::TYPE_STOCK_TRANSFER_PENDING => 'primary',

            self::TYPE_POS_SESSION_OPEN => 'success',
            self::TYPE_POS_CASH_DISCREPANCY => 'danger',

            self::TYPE_UNPOSTED_JOURNAL_ENTRY => 'warning',

            self::TYPE_ASSET_MAINTENANCE => 'primary',
            self::TYPE_ASSET_DEPRECIATION => 'warning',

            self::TYPE_SYSTEM_ALERT => 'info',
        ];

        return $colors[$this->notification_type] ?? 'secondary';
    }

    // Scope for active notifications (Pending)
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Scope by notification type
    public function scopeOfType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    // Scope by module
    public function scopeByModule($query, string $module)
    {
        $types = array_keys(array_filter(self::typeModuleMap(), fn ($mod) => $mod === $module));

        return $query->whereIn('notification_type', $types);
    }

    /**
     * Strict User, Role & Permission Scoping.
     */
    public function scopeForUser($query, ?\App\Models\User $user = null)
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // المالك يرى جميع الإشعارات
        if (method_exists($user, 'hasRole') && $user->hasRole('owner')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {

            // 1. إشعارات موجهة مباشرةً للمستخدم بـ user_id
            $q->where('user_id', $user->id)
                ->orWhere(function ($sub) use ($user) {
                    $sub->where('notifiable_type', \App\Models\User::class)
                        ->where('notifiable_id', $user->id);
                });

            // 2. إشعارات موجهة بالصلاحية أو الدور (بدون user_id محدد)
            $q->orWhere(function ($sub) use ($user) {
                $sub->whereNull('user_id');

                $sub->where(function ($inner) use ($user) {

                    // 2a. إشعارات بـ target_permission: يرى المستخدم الإشعار إذا كان يملك تلك الصلاحية
                    $inner->where(function ($permQ) use ($user) {
                        $permQ->whereNotNull('target_permission');

                        if (method_exists($user, 'can')) {
                            // جمع كل الصلاحيات الموجودة في notification_log_items
                            // والتحقق من أن المستخدم يملك كل منها
                            $allPerms = self::distinct()
                                ->whereNotNull('target_permission')
                                ->pluck('target_permission')
                                ->unique()
                                ->filter(fn ($perm) => $user->can($perm))
                                ->values()
                                ->toArray();

                            if (! empty($allPerms)) {
                                $permQ->whereIn('target_permission', $allPerms);
                            } else {
                                $permQ->whereRaw('1 = 0');
                            }
                        } else {
                            $permQ->whereRaw('1 = 0');
                        }
                    });

                    // 2b. إشعارات بـ target_role: يرى المستخدم الإشعار إذا كان يملك ذلك الدور
                    if (method_exists($user, 'roles')) {
                        $userRoles = $user->roles()->pluck('name')->toArray();
                        if (! empty($userRoles)) {
                            $inner->orWhere(function ($roleQ) use ($userRoles) {
                                $roleQ->whereNotNull('target_role')
                                    ->whereIn('target_role', $userRoles);
                            });
                        }
                    }

                    // 2c. إشعارات بدون target_permission وبدون target_role
                    // تظهر بناءً على نوع الإشعار وصلاحيات المستخدم (fallback)
                    $permittedTypes = [];
                    foreach (self::typePermissions() as $type => $permission) {
                        if ($permission === null || (method_exists($user, 'can') && $user->can($permission))) {
                            $permittedTypes[] = $type;
                        }
                    }

                    if (! empty($permittedTypes)) {
                        $inner->orWhere(function ($fallQ) use ($permittedTypes) {
                            $fallQ->whereNull('target_permission')
                                ->whereNull('target_role')
                                ->whereIn('notification_type', $permittedTypes);
                        });
                    }
                });
            });
        });
    }

    public function cancel()
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function confirm()
    {
        return $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    public function markAsRead()
    {
        return $this->update([
            'status' => self::STATUS_READ,
            'read_at' => now(),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function notifiable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function getTargetUserNameAttribute(): ?string
    {
        if ($this->user && $this->user->name) {
            return $this->user->name;
        }

        if ($this->notifiable) {
            if (isset($this->notifiable->employee) && $this->notifiable->employee) {
                $emp = $this->notifiable->employee;

                if (is_string($emp)) {
                    return $emp;
                }

                return $emp->name 
                    ?? $emp->main_employee?->full_name 
                    ?? $emp->main_employee?->name 
                    ?? $emp->user?->name 
                    ?? null;
            }
            if (isset($this->notifiable->driver) && $this->notifiable->driver) {
                return $this->notifiable->driver->name ?? $this->notifiable->driver->full_name ?? null;
            }
            if (isset($this->notifiable->name)) {
                return $this->notifiable->name;
            }
            if (isset($this->notifiable->full_name)) {
                return $this->notifiable->full_name;
            }
        }

        return null;
    }

    public function getExtraAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_null($value)) {
            return [];
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded ?? [];
        }

        return [];
    }
}
