<?php

namespace App\Http\Controllers;

use App\Models\NotificationItem;
use Illuminate\Http\Request;
use App\Repositories\NotificationRepository;
use Carbon\Carbon;
use Spatie\NotificationLog\Models\NotificationLogItem;

class NotificationController extends Controller
{
    /** @var NotificationRepository */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepository = $notificationRepo;
    }

    public function index(Request $request)
    {
        $setting = hr_setting();
        $data = $request->all();

        // تحديد نوع الإشعار، استخدم القيمة من الإعدادات إذا لم يرسل في الطلب
        $notificationType = $data['notification_type'] ?? $setting->tap;
        $data['notification_type'] = $notificationType;

        // تعيين الحالة الافتراضية
        $data['status'] = NotificationItem::STATUS_PENDING;

        // تحديث الإعدادات إذا اختلف النوع عن القيمة الحالية
        if ($setting->tap !== $notificationType) {
            $setting->tap = $notificationType;
            $setting->save();
        }

        // جلب الإشعارات حسب الحالة والنوع
        $data['notifications'] = $this->notificationRepository
            ->allQuery($data)
            ->latest()
            ->paginate($request->pagination ?? 20);

        // جلب المستخدمين وأنواع الإشعارات
        $data['users'] = $this->notificationRepository->user();
        $data['types'] = $this->notificationRepository->types();
        $data['setting'] = $setting;

        return view('notifications.index', $data);
    }

    public function read($id)
    {
        $notifications = $this->notificationRepository->find($id);

        $this->notificationRepository->markAsRead($id, auth()->id());

        return redirect()->route('notifications.index');
    }

    public function edit($id)
    {
        $notifications = $this->notificationRepository->find($id);

        if ($notifications) {
            $route = $this->notificationRepository->route($notifications->notification_type, $notifications->notifiable_id);
            return redirect($route);
        }

        return back();
    }
    // public function markAsRead($id)
    // {
    //     $success = $this->notificationRepository->markAsRead($id);

    //     if ($success) {
    //         flash(__('notifications.notification_marked_read'))->success();
    //     } else {
    //         flash(__('notifications.notification_not_found'))->error();
    //     }

    //     return redirect()->route('notifications.index');
    // }

    // public function markAllAsRead()
    // {
    //     $this->notificationRepository->markAllAsRead(auth()->id());

    //     flash(__('notifications.all_notifications_marked_read'))->success();

    //     return redirect()->route(route: 'notifications.index');
    // }

    public function destroy($id)
    {
        $notification = $this->notificationRepository->find($id);

        if (empty($notification)) {
            flash()->error(__('models/notifications.singular') . ' ' . __('messages.not_found'));

            return redirect(route('notifications.index'));
        }

        $notification->status = NotificationItem::STATUS_CANCELLED;
        $notification->read_at = Carbon::today();
        $notification->save();

        flash()->success(__('messages.deleted', ['model' => __('models/notifications.singular')]));

        return redirect(route('notifications.index'));
    }

    // public function clearRead()
    // {
    //     try {
    //         $this->notificationRepository->clearRead(auth()->id());
    //         flash(__('notifications.read_notifications_cleared'))->success();
    //     } catch (\Exception $e) {
    //         // Log the error if needed
    //         flash(__('notifications.error_occurred'))->error();
    //     }

    //     return redirect()->route('notifications.index');
    // }
}
