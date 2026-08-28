<?php

namespace App\Services;

use App\Models\NotificationItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Send a notification with strict user, permission, or role targeting and priority level.
     */
    public static function send(
        string $type,
        string $title,
        ?string $body = null,
        ?Model $notifiable = null,
        ?int $userId = null,
        ?string $targetPermission = null,
        ?string $targetRole = null,
        int $priority = NotificationItem::PRIORITY_NORMAL,
        ?string $channel = NotificationItem::CHANNEL_DATABASE,
        array $extra = []
    ): ?NotificationItem {
        $fingerprint = md5(implode('|', [
            $type,
            $userId ?? 'all',
            $targetPermission ?? 'no_perm',
            $targetRole ?? 'no_role',
            $notifiable ? get_class($notifiable) : '',
            $notifiable ? $notifiable->getKey() : '',
            $title
        ]));

        $existing = NotificationItem::where('fingerprint', $fingerprint)
            ->where('status', NotificationItem::STATUS_PENDING)
            ->first();

        if ($existing) {
            $existing->update([
                'title' => $title,
                'body' => $body,
                'priority' => $priority,
                'extra' => array_merge($existing->extra ?? [], $extra),
                'updated_at' => now(),
            ]);

            return $existing;
        }

        return NotificationItem::create([
            'notification_type' => $type,
            'title' => $title,
            'body' => $body,
            'user_id' => $userId,
            'target_permission' => $targetPermission,
            'target_role' => $targetRole,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable ? $notifiable->getKey() : null,
            'channel' => $channel ?? NotificationItem::CHANNEL_DATABASE,
            'status' => NotificationItem::STATUS_PENDING,
            'priority' => $priority,
            'fingerprint' => $fingerprint,
            'extra' => $extra,
        ]);
    }

    /**
     * Send private notification targeted to a SPECIFIC USER ONLY.
     */
    public static function sendToUser(
        int $userId,
        string $type,
        string $title,
        ?string $body = null,
        ?Model $notifiable = null,
        int $priority = NotificationItem::PRIORITY_NORMAL,
        array $extra = []
    ): ?NotificationItem {
        return self::send($type, $title, $body, $notifiable, $userId, null, null, $priority, NotificationItem::CHANNEL_DATABASE, $extra);
    }

    /**
     * Send notification targeted to users with a SPECIFIC PERMISSION.
     */
    public static function sendToPermission(
        string $permission,
        string $type,
        string $title,
        ?string $body = null,
        ?Model $notifiable = null,
        int $priority = NotificationItem::PRIORITY_NORMAL,
        array $extra = []
    ): ?NotificationItem {
        return self::send($type, $title, $body, $notifiable, null, $permission, null, $priority, NotificationItem::CHANNEL_DATABASE, $extra);
    }

    /**
     * Send URGENT notification.
     */
    public static function sendUrgent(
        string $type,
        string $title,
        ?string $body = null,
        ?Model $notifiable = null,
        ?int $userId = null,
        ?string $targetPermission = null,
        array $extra = []
    ): ?NotificationItem {
        return self::send($type, $title, $body, $notifiable, $userId, $targetPermission, null, NotificationItem::PRIORITY_URGENT, NotificationItem::CHANNEL_DATABASE, $extra);
    }

    /**
     * Cancel active notifications.
     */
    public static function cancel(string $type, ?Model $notifiable = null, ?int $userId = null): int
    {
        $query = NotificationItem::where('notification_type', $type)
            ->where('status', NotificationItem::STATUS_PENDING);

        if ($notifiable) {
            $query->where('notifiable_type', get_class($notifiable))
                  ->where('notifiable_id', $notifiable->getKey());
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->update(['status' => NotificationItem::STATUS_CANCELLED]);
    }
}
