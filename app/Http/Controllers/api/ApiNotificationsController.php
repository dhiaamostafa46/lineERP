<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\NotificationItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Repositories\HrEmployeeRepository; // Added DB facade
use Modules\HR\App\Traits\ApiResponses;

class ApiNotificationsController extends Controller
{
    use ApiResponses;

    /** @var HrEmployeeRepository */
    private $hrEmployeeRepository;

    public function __construct(HrEmployeeRepository $HREmployeeRepository)
    {
        $this->hrEmployeeRepository = $HREmployeeRepository;
    }

    public function getRequests($lang, Request $request)
    {

        $limit = $request->get('limit', 20);
        $lastId = $request->get('last_id');

        app()->setLocale($lang);

        $query = NotificationItem::where('status', NotificationItem::STATUS_PENDING)
            ->where('user_id', auth()->user()->id)->where('channel', 'mobile_push')
            ->orderBy('id', 'desc');
        // $count = $user->notifications()->whereNull('read_at')->count();
        $count = NotificationItem::where('status', NotificationItem::STATUS_PENDING)
            ->where('user_id', auth()->user()->id)->where('channel', 'mobile_push')
            ->whereNull('read_at')->count();

        if ($lastId) {
            $query->where('id', '<', $lastId);
        }
        $records = $query->limit($limit)->get();

        $employee = auth()->user()->employee()->first();
        //
        $requests = [];
        foreach ($records as $record) {
            $requests[] = [
                'id' => $record->id,
                'title' => $record->title,
                'body' => $record->body,
                'read' => $record->read_at ? true : false,
                'datetime' => $record->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json(
            [
                'status_code' => '00',
                'employee_name' => $employee->full_name,
                'unread_count' => $count,
                'requests' => $requests,
                'meta' => [
                    'last_id' => optional($records->last())->id,
                    'has_more' => $records->count() == $limit,
                ],
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE,
        );
    }

    public function markAsRead($id, $lang)
    {
        // $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $result = NotificationItem::where('id', $id)->update(['read_at' => Carbon::now()]);

        return response()->json([
            'status_code' => '00',
            'message' => 'done',
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
