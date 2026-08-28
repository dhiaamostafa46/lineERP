<?php

namespace App\Http\Controllers;

use App\Models\NotificationItem;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;

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
        $user = auth()->user();

        $query = NotificationItem::with(['user', 'notifiable'])->forUser($user)->latest();

        if ($request->filled('module') && $request->module !== 'all') {
            $query->byModule($request->module);
        }

        if ($request->filled('notification_type')) {
            $query->where('notification_type', $request->notification_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
        }

        $notifications = $query->paginate($request->pagination ?? 20);

        $modules = NotificationItem::userModules($user);
        $moduleIcons = NotificationItem::moduleIcons();
        $types = NotificationItem::types();
        $statuses = NotificationItem::statuses();
        $users = $this->notificationRepository->user();

        $countsByModule = [];
        $countsByModule['all'] = NotificationItem::forUser($user)->active()->count();
        foreach ($modules as $modKey => $label) {
            $countsByModule[$modKey] = NotificationItem::forUser($user)->active()->byModule($modKey)->count();
        }

        return view('notifications.index', compact(
            'notifications',
            'modules',
            'moduleIcons',
            'types',
            'statuses',
            'users',
            'countsByModule'
        ));
    }

    public function read($id)
    {
        $this->notificationRepository->markAsRead($id);
        $notification = NotificationItem::find($id);

        if ($notification) {
            $route = $this->notificationRepository->route($notification->notification_type, $notification->notifiable_id, $notification->extra ?? []);

            return redirect($route);
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead()
    {
        $this->notificationRepository->markAllAsRead(auth()->id());
        flash(__('models/notifications.all_marked_as_read'))->success();

        return redirect()->route('notifications.index');
    }

    public function edit($id)
    {
        $notification = NotificationItem::find($id);

        if ($notification) {
            $notification->markAsRead();
            $route = $this->notificationRepository->route($notification->notification_type, $notification->notifiable_id, $notification->extra ?? []);

            return redirect($route);
        }

        return back();
    }

    public function confirm($id)
    {
        $notification = NotificationItem::find($id);
        if ($notification) {
            $notification->confirm();
            flash(__('models/notifications.status.confirmed'))->success();
        }

        return redirect()->route('notifications.index');
    }

    public function destroy($id)
    {
        $notification = NotificationItem::find($id);

        if (empty($notification)) {
            flash()->error(__('models/notifications.singular').' '.__('messages.not_found'));

            return redirect(route('notifications.index'));
        }

        $notification->cancel();
        flash()->success(__('messages.deleted', ['model' => __('models/notifications.singular')]));

        return redirect(route('notifications.index'));
    }
}
