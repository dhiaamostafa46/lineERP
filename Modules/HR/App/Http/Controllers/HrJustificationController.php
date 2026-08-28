<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\HR\App\Http\Requests\CreateHrJustificationRequest;
use Modules\HR\App\Http\Requests\UpdateHrJustificationRequest;
use Modules\HR\App\Repositories\HrJustificationRepository;
use Modules\HR\App\Models\HrEmployee;

use Modules\HR\App\Models\HrTimeTrack;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;

class HrJustificationController extends AppBaseController
{
    /** @var HrJustificationRepository $hrJustificationRepository*/
    private $hrJustificationRepository;

    public function __construct(HrJustificationRepository $hrJustificationRepo)
    {
        $this->hrJustificationRepository = $hrJustificationRepo;
    }

    /**
     * Display a listing of the HrJustification.
     */
    public function index(Request $request)
    {

        $data['justifications'] = $this->hrJustificationRepository->allQuery($request->all())->latest()->paginate($request->pagination ?? 15);
        $data['statuses'] = $this->hrJustificationRepository->statuses();
        $data['types'] = $this->hrJustificationRepository->types();
        $data['employees'] = $this->hrJustificationRepository->employees();

        return view('hr::justifications.index', $data);
    }

    /**
     * Show the form for creating a new HrJustification.
     */
    public function create()
    {
        $data['employees'] = $this->hrJustificationRepository->employees();
        $data['attendances'] = $this->hrJustificationRepository->attendances();
        $data['types'] = $this->hrJustificationRepository->types();

        return view('hr::justifications.create', $data);
    }

    /**
     * Store a newly created HrJustification in storage.
     */
    public function store(CreateHrJustificationRequest $request)
    {


        $input = $request->all();
        
        foreach(['from_time', 'to_time'] as $field) {
            if (isset($input[$field]) && $input[$field] === 'null') {
                $input[$field] = null;
            }
        }

       // dd(   $input );
        $justification = $this->hrJustificationRepository->create($input);
        $this->hrJustificationRepository->checkTracking($justification);

        flash(__('messages.saved', ['model' => __('hr::models/hr_justifications.singular')]), 'success');

        return redirect(route('hr.empdashboard.index'));
    }


    /**
     * Get attendances for a specific employee.
     */
   public function getAttendancesForEmployee(Request $request)
{
    $employeeId = $request->input('employee_id');

    $employee = HrEmployee::find($employeeId);

    if (!$employee) {
        return response()->json([]);
    }

    // Assuming 'shift' is a relationship on the HrEmployee model that can return multiple shifts.
    // The original code was broken, so I'm assuming the relationship is named 'shift' and is a collection.
    $shifts = optional($employee->shift->shifts)->mapWithKeys(function ($item) {
        return [
            $item->id => "{$item->from} - {$item->to}"
        ];
    }) ?? collect();



    $allOptions = $shifts;

    return response()->json($allOptions);
}




    /**
     * Display the specified HrJustification.
     */
    public function show($id)
    {
        $justification = $this->hrJustificationRepository->find($id);

        if (empty($justification)) {
            flash(__('messages.not_found', ['model' => __('hr::models/hr_justifications.singular')]), 'error');

            return redirect(route('hr.justifications.index'));
        }

        return view('hr::justifications.show')->with('justification', $justification);
    }

    /**
     * Show the form for editing the specified HrJustification.
     */
    public function edit($id)
    {
        $justification = $this->hrJustificationRepository->find($id);

        if (empty($justification)) {
            flash(__('messages.not_found', ['model' => __('hr::models/hr_justifications.singular')]), 'error');

            return redirect(route('hr.justifications.index'));
        }
        $data['justification'] =$justification;
        $data['employees'] = $this->hrJustificationRepository->employees();
        $data['attendances'] = $this->hrJustificationRepository->attendances();
        $data['types'] = $this->hrJustificationRepository->types();

        return view('hr::justifications.edit', $data);
    }

    /**
     * Update the specified HrJustification in storage.
     */
    public function update($id, UpdateHrJustificationRequest $request)
    {
        $justification = $this->hrJustificationRepository->find($id);

        if (empty($justification)) {
            flash(__('messages.not_found', ['model' => __('hr::models/hr_justifications.singular')]), 'error');

            return redirect(route('hr.justifications.index'));
        }

        $input = $request->all();
        
        foreach(['from_time', 'to_time'] as $field) {
            if (isset($input[$field]) && $input[$field] === 'null') {
                $input[$field] = null;
            }
        }

        $justification = $this->hrJustificationRepository->update($input, $id);

        flash(__('messages.updated', ['model' => __('hr::models/hr_justifications.singular')]), 'success');

        return redirect(route('hr.justifications.index'));
    }
}
