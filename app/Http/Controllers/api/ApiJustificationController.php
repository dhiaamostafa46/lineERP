<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\ApiJutificationRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrJustification;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Repositories\HrJustificationRepository;
use Modules\HR\App\Repositories\HrMonthlyPaymentRepository; // Added DB facade
use Modules\HR\App\Traits\ApiResponses;

class ApiJustificationController extends Controller
{
    use ApiResponses;

    /** @var HrEmployeeRepository */
    private $hrEmployeeRepository;

    /** @var HrJustificationRepository */
    private $JustificationRepository;

    /** @var HrMonthlyPaymentRepository */
    private $HrMonthlyPaymentRepository;

    public function __construct(HrEmployeeRepository $HREmployeeRepository, HrJustificationRepository $JustificationRepository)
    {

        $this->hrEmployeeRepository = $HREmployeeRepository;
        $this->JustificationRepository = $JustificationRepository;
    }

    public function getRequests($lang, Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        app()->setLocale($lang);
        $employee = auth()->user()->employee()->first();
        $records = HrJustification::where('employee_id', $employee->HrEmployee->id)->latest()->get();

        $result = null;
        if (($request->filled('from')) && ($request->filled('to'))) {
            $records = HrJustification::where('employee_id', $employee->hrEmployee->id)
                ->whereDate('created_at', '>=', $request->from)
                ->whereDate('created_at', '<=', $request->to)->latest()->get();

        }

        // getAttachmentOriginalPathAttribute
        // getAttachmentUrlAttribute
        // getAttachmentInfoAttribute
        $requests = [];
        foreach ($records as $record) {

            $requests[] =
                 [
               'request_id' => $record->id,
               'request_date' => $record->created_at->format('d-m-Y'),
               'reason' => $record->reason,
               'type' => $record->type, // 1 = late, 2 = EARLY_LEAVE, 3 = ABSENCE
               'file_path' => $record->getAttachmentUrlAttribute(),
               'status' => $record->status, // 1 = pending, 2 = approved, 3 = rejected
                 ];
        }

        return response()->json([
            'status_code' => '00',
            'employee_name' => $employee->full_name,
            'requests' => $requests,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store($lang, ApiJutificationRequest $request)
    {
        app()->setLocale($lang);
        $input = $request->all();
        $employee = auth()->user()->employee()->first();

        $input['employee_id'] = $employee->hrEmployee->id;
        $input['from_time'] = '09:00';
        $input['to_time'] = '13:00';
        DB::beginTransaction();
        try {
            $justif = $this->JustificationRepository->create($input);

            $this->JustificationRepository->checkTracking($justif);
            DB::commit();

            return response()->json([
                'status_code' => '00',
                'request_id' => $justif->id,
                'request_no' => 'ADJ-'.Carbon::now()->format('y').'-'.$justif->id,
                'message' => __('messages.request_add'),
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status_code' => '500',
                'message' => $e->getMessage(),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
