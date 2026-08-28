<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrShiftTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrShiftTypeRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Models\HrShiftType;
use Modules\HR\App\Repositories\HrShiftTypeRepository;
use Illuminate\Http\Request;


class HrShiftTypeController extends AppBaseController
{
    /** @var HrShiftTypeRepository $hrShiftTypeRepository*/
    private $hrShiftTypeRepository;

    public function __construct(HrShiftTypeRepository $hrShiftTypeRepo)
    {
        $this->hrShiftTypeRepository = $hrShiftTypeRepo;
    }


    /**
     * Display a listing of the HrShiftType.
     */
    public function index(Request $request)
    {
        $data['shifts'] = $this->hrShiftTypeRepository->allQuery($request->except('pagination'))->latest()->paginate($request->pagination ?? 10);
        $data['statuses'] = $this->hrShiftTypeRepository->statuses();
        $data['types'] = $this->hrShiftTypeRepository->types();
        return view('hr::shift_types.index', $data);
    }

    /**
     * Show the form for creating a new HrShiftType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrShiftTypeRepository->statuses();
        $data['types'] = $this->hrShiftTypeRepository->types();
        return view('hr::shift_types.create', $data);
    }

    /**
     * Store a newly created HrShiftType in storage.
     */
    public function store(CreateHrShiftTypeRequest $request)
    {
        $input = $request->all();
        $shift = $this->hrShiftTypeRepository->create($input);
        $shift = $this->hrShiftTypeRepository->create_shifts($input['shifts'], $shift);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_shift_types.singular')]));

        return redirect(route('hr.shift_types.index'));
    }

    /**
     * Display the specified HrShiftType.
     */
    public function show($id)
    {
        $shift = $this->hrShiftTypeRepository->find($id);

        if (empty($shift)) {
            flash()->error(__('hr::models/hr_shift_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.shift_types.index'));
        }

        return view('hr::shift_types.show')->with('hrShiftType', $shift);
    }

    /**
     * Show the form for editing the specified HrShiftType.
     */
    public function edit($id)
    {
        $data['shift'] = $this->hrShiftTypeRepository->find($id);
        if (empty($data['shift'])) {
            flash()->error(__('hr::models/hr_shift_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.shift_types.index'));
        }
        $data['statuses'] = $this->hrShiftTypeRepository->statuses();
        $data['types'] = $this->hrShiftTypeRepository->types();

        return view('hr::shift_types.edit', $data);
    }

    /**
     * Update the specified HrShiftType in storage.
     */
    public function update($id, UpdateHrShiftTypeRequest $request)
    {
        $shift = $this->hrShiftTypeRepository->find($id);

        if (empty($shift)) {
            flash()->error(__('hr::models/hr_shift_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.shift_types.index'));
        }


        //dd($request->all());
        $shift = $this->hrShiftTypeRepository->update($request->all(), $id);
        $shift = $this->hrShiftTypeRepository->update_shifts($request->shifts, $shift);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_shift_types.singular')]));

        return redirect(route('hr.shift_types.index'));
    }

    /**
     * Remove the specified HrShiftType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $shift = $this->hrShiftTypeRepository->find($id);

        if (empty($shift)) {
            flash()->error(__('hr::models/hr_shift_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.shift_types.index'));
        }

        $this->hrShiftTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_shift_types.singular')]));

        return redirect(route('hr.shift_types.index'));
    }
}
