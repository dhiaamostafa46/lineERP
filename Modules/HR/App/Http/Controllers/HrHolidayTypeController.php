<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrHolidayTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrHolidayTypeRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrHolidayTypeRepository;
use Illuminate\Http\Request;
use Modules\HR\App\Repositories\HrHolidayBalanceRepository;


class HrHolidayTypeController extends AppBaseController
{
    /** @var HrHolidayTypeRepository $hrHolidayTypeRepository*/
    private $hrHolidayTypeRepository;
    private $HrHolidayBalanceRepository;

    public function __construct(HrHolidayTypeRepository $hrHolidayTypeRepo ,HrHolidayBalanceRepository $HrHolidayBalanceRepository)
    {
        $this->hrHolidayTypeRepository = $hrHolidayTypeRepo;
        $this->HrHolidayBalanceRepository = $HrHolidayBalanceRepository;
    }

    /**
     * Display a listing of the HrHolidayType.
     */
    public function index(Request $request)
    {
        $data['holiday_types'] = $this->hrHolidayTypeRepository->paginate(10);
        $data['statuses'] = $this->hrHolidayTypeRepository->statuses();
        return view('hr::holiday_types.index', $data);
    }


    /**
     * Show the form for creating a new HrHolidayType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrHolidayTypeRepository->statuses();
        $data['types'] = $this->hrHolidayTypeRepository->types();

        return view('hr::holiday_types.create', $data);
    }

    /**
     * Store a newly created HrHolidayType in storage.
     */
    public function store(CreateHrHolidayTypeRequest $request)
    {
        $input = $request->all();

        $report_type = $this->hrHolidayTypeRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_holiday_types.singular')]));

        return redirect(route('hr.holiday_types.index'));
    }

    /**
     * Display the specified HrHolidayType.
     */
    public function show($id)
    {
        $data['holiday_type'] = $this->hrHolidayTypeRepository->find($id);

        if (empty($data['holiday_type'])) {
            flash()->error(__('hr::models/hr_holiday_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holiday_types.index'));
        }

        return view('hr::holiday_types.show', $data);
    }

    /**
     * Show the form for editing the specified HrHolidayType.
     */
    public function edit($id)
    {
        $data['holiday_type'] = $this->hrHolidayTypeRepository->find($id);
        $data['types'] = $this->hrHolidayTypeRepository->types();

        if (empty($data['holiday_type'])) {
            flash()->error(__('hr::models/hr_holiday_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holiday_types.index'));
        }
        $data['statuses'] = $this->hrHolidayTypeRepository->statuses();

        return view('hr::holiday_types.edit', $data);
    }

    /**
     * Update the specified HrHolidayType in storage.
     */
    public function update($id, UpdateHrHolidayTypeRequest $request)
    {
        $holiday_type = $this->hrHolidayTypeRepository->find($id);

        if (empty($holiday_type)) {
            flash()->error(__('hr::models/hr_holiday_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holiday_types.index'));
        }

        $holiday_type = $this->hrHolidayTypeRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_holiday_types.singular')]));

        return redirect(route('hr.holiday_types.index'));
    }

    /**
     * Remove the specified HrHolidayType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $holiday_type = $this->hrHolidayTypeRepository->find($id);

        if (empty($holiday_type)) {
            flash()->error(__('hr::models/hr_holiday_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holiday_types.index'));
        }

        $this->hrHolidayTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_holiday_types.singular')]));

        return redirect(route('hr.holiday_types.index'));
    }
}
