<?php

namespace App\Livewire;

use App\Models\NotificationItem;
use App\Repositories\NotificationRepository;
use Livewire\Component;

class Notifications extends Component
{
    public $activeModule = 'all';

    public function setModule($module)
    {
        $this->activeModule = $module;
    }

    public function markAsRead($id)
    {
        /** @var NotificationRepository $repository */
        $repository = app(NotificationRepository::class);
        $repository->markAsRead($id);

        $notification = NotificationItem::find($id);
        if ($notification) {
            $url = $repository->route($notification->notification_type, $notification->notifiable_id, $notification->extra ?? []);

            return redirect()->to($url);
        }
    }

    public function markAllAsRead()
    {
        /** @var NotificationRepository $repository */
        $repository = app(NotificationRepository::class);
        $repository->markAllAsRead();
    }

    public function render()
    {
        $user = auth()->user();

        $query = NotificationItem::forUser($user)
            ->active()
            ->where('channel', '!=', NotificationItem::CHANNEL_MOBILE_PUSH)
            ->latest('id');

        if ($this->activeModule !== 'all') {
            $query->byModule($this->activeModule);
        }

        $notifications = $query->take(25)->get();

        $unreadCount = NotificationItem::forUser($user)
            ->active()
            ->where('channel', '!=', NotificationItem::CHANNEL_MOBILE_PUSH)
            ->count();

        $modules = NotificationItem::userModules($user);
        $moduleIcons = NotificationItem::moduleIcons();

        return view('livewire.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'modules' => $modules,
            'moduleIcons' => $moduleIcons,
            'activeModule' => $this->activeModule,
        ]);
    }
}
