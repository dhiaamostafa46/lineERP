<?php

namespace App\Repositories;

use App\Models\NotificationItem;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;

class NotificationRepository extends BaseRepository
{
    public function model(): string
    {
        return NotificationItem::class;
    }

    protected $fieldSearchable = [
        'org_id',
        'user_id',
        'notification_type',
        'notifiable_id',
        'notifiable_type',
        'channel',
        'status',
        'fingerprint',
        'read_at',
        'extra',
        'anonymous_notifiable_properties',
        'confirmed_at'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Get all active notifications scoped for current user permissions.
     */
    public function getUserNotifications(?int $userId = null): Collection
    {
        $user = $userId ? User::find($userId) : auth()->user();
        return $this->model->forUser($user)->latest()->get();
    }

    public function user(): array
    {
        return User::pluck('name', 'id')->toArray();
    }

    public function types(): array
    {
        return NotificationItem::types();
    }

    public function modules(): array
    {
        return NotificationItem::modules();
    }

    public function findUserNotification(int $id)
    {
        return $this->model->forUser(auth()->user())->find($id);
    }

    public function route($type, $id = null, array $extra = []): string
    {
        $routeName = null;
        $params = $id ? [$id] : [];

        switch ($type) {
            // HR
            case NotificationItem::TYPE_IQAMA_EXPIRY:
            case NotificationItem::TYPE_INSURANCE_EXPIRY:
            case NotificationItem::TYPE_PASSPORT_EXPIRY:
                $routeName = 'hr.employees.edit';
                break;
            case NotificationItem::TYPE_LEAVE_REQUEST:
                $routeName = 'hr.holidays.show';
                break;
            case NotificationItem::TYPE_ADVANCE_REQUEST:
                $routeName = 'hr.advances.show';
                break;
            case NotificationItem::TYPE_SETTLEMENT_REQUEST:
                $routeName = 'hr.justifications.show';
                break;

            // Vehicles
            case NotificationItem::TYPE_VEHICLE_LICENSE_EXPIRY:
                $routeName = 'vehicles.show';
                break;
            case NotificationItem::TYPE_DRIVER_LICENSE_EXPIRY:
                $routeName = 'drivers.show';
                break;
            case NotificationItem::TYPE_MAINTENANCE_REQUEST:
                $routeName = 'vc_maintenance_requests.show';
                break;
            case NotificationItem::TYPE_TRAFFIC_VIOLATION:
                $routeName = 'dr_traffic_violations.index';
                $params = [];
                break;

            // Invoices
            case NotificationItem::TYPE_QUOTATION_EXPIRED:
                $routeName = 'quotations.show';
                break;
            case NotificationItem::TYPE_INVOICE_DUE:
                $routeName = 'sales_invoices.show';
                break;
            case NotificationItem::TYPE_PURCHASE_RETURN_PENDING:
                $routeName = 'purchase_return_invoices.show';
                break;

            // Store
            case NotificationItem::TYPE_LOW_STOCK:
                $routeName = 'store.reports.lowStock.index';
                $params = [];
                break;
            case NotificationItem::TYPE_STOCK_TRANSFER_PENDING:
                $routeName = 'st_transfers.index';
                $params = [];
                break;

            // POS
            case NotificationItem::TYPE_POS_SESSION_OPEN:
            case NotificationItem::TYPE_POS_CASH_DISCREPANCY:
                $routeName = 'pos.sessions.index';
                $params = [];
                break;

            // Accounting
            case NotificationItem::TYPE_UNPOSTED_JOURNAL_ENTRY:
                $routeName = 'accusoft.journal_entries.index';
                $params = [];
                break;

            default:
                $routeName = 'notifications.index';
                $params = [];
                break;
        }

        if ($routeName && Route::has($routeName)) {
            return route($routeName, $params);
        }

        return route('notifications.index');
    }

    public function markAsRead(int $id): bool
    {
        $notification = $this->model->find($id);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    public function markAllAsRead(?int $userId = null): int
    {
        $user = $userId ? User::find($userId) : auth()->user();
        return $this->model
            ->forUser($user)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'status' => NotificationItem::STATUS_READ
            ]);
    }

    public function clearRead(?int $userId = null): int
    {
        $user = $userId ? User::find($userId) : auth()->user();
        return $this->model
            ->forUser($user)
            ->whereNotNull('read_at')
            ->delete();
    }
}
