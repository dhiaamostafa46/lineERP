<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrHolidayRequest;
use Modules\HR\App\Http\Requests\UpdateHrHolidayRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrHolidayRepository;
use Illuminate\Http\Request;

class HrHolidayController extends AppBaseController
{
    /** @var HrHolidayRepository $hrHolidayRepository*/
    private $hrHolidayRepository;

    public function __construct(HrHolidayRepository $hrHolidayRepo)
    {
        $this->hrHolidayRepository = $hrHolidayRepo;
    }

    /**
     * Display a listing of the HrHoliday.
     */
    public function index(Request $request)
    {
        $data['holidays'] = $this->hrHolidayRepository->paginate(10);
        $data['employees'] = $this->hrHolidayRepository->employees();
        $data['types'] = $this->hrHolidayRepository->types();
        $data['statuses'] = $this->hrHolidayRepository->statuses();
        return view('hr::holidays.index', $data);
    }

    /**
     * Show the form for creating a new HrHoliday.
     */
    public function create()
    {
        $data['employees'] = $this->hrHolidayRepository->employees();
        $data['types'] = $this->hrHolidayRepository->types();
        $data['statuses'] = $this->hrHolidayRepository->statuses();

        return view('hr::holidays.create', $data);
    }

    /**
     * Store a newly created HrHoliday in storage.
     */
    public function store(CreateHrHolidayRequest $request)
    {
        $input = $request->all();
        //  dd( $input );
        $holiday = $this->hrHolidayRepository->create($input);
        $this->hrHolidayRepository->checkTracking($holiday);

        flash()->success(__('messages.saved', ['model' => __('models/hr_holidays.singular')]));

        if (str_contains($_SERVER['HTTP_REFERER'], 'my-requests')) {
            return redirect(route('hr.empdashboard.index'));
            //  return back();
        }

        return redirect(route('hr.holidays.index'));
        //    return back();
    }

    /**
     * Display the specified HrHoliday.
     */
    public function show($id)
    {
        $holiday = $this->hrHolidayRepository->find($id);
        $data['holiday'] = $holiday;

        $data['balance'] = $this->hrHolidayRepository->balance($holiday->employee_id, $holiday->type_id);

        if (empty($data['holiday'])) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        return view('hr::holidays.show', $data);
    }

    /**
     * Show the form for editing the specified HrHoliday.
     */
    public function edit($id)
    {
        $data['holiday'] = $this->hrHolidayRepository->find($id);
        if (empty($data['holiday'])) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        $data['employees'] = $this->hrHolidayRepository->employees();
        $data['types'] = $this->hrHolidayRepository->types();
        $data['statuses'] = $this->hrHolidayRepository->statuses();

        return view('hr::holidays.edit', $data);
    }

    public function balance($id, $type)
    {
        $data = $this->hrHolidayRepository->balance($id, $type);

        return response()->json($data);
    }

    /**
     * Update the specified HrHoliday in storage.
     */
    public function update($id, UpdateHrHolidayRequest $request)
    {
        $holiday = $this->hrHolidayRepository->find($id);

        if (empty($holiday)) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        $holiday = $this->hrHolidayRepository->update($request->all(), $id);
        $this->hrHolidayRepository->checkTracking($holiday);

        flash()->success(__('messages.updated', ['model' => __('models/hr_holidays.singular')]));

        return redirect(route('hr.holidays.index'));
    }

    /**
     * Remove the specified HrHoliday from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $holiday = $this->hrHolidayRepository->find($id);

        if (empty($holiday)) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        $this->hrHolidayRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hr_holidays.singular')]));

        return redirect(route('hr.holidays.index'));
    }
}
