<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrPenaltyRequest;
use Modules\HR\App\Http\Requests\UpdateHrPenaltyRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrPenaltyRepository;
use Illuminate\Http\Request;

class HrPenaltyController extends AppBaseController
{
    /** @var HrPenaltyRepository $hrPenaltyRepository*/
    private $hrPenaltyRepository;

    public function __construct(HrPenaltyRepository $hrPenaltyRepo)
    {
        $this->hrPenaltyRepository = $hrPenaltyRepo;
    }

    /**
     * Display a listing of the HrPenalty.
     */
    public function index(Request $request)
    {
        $data['penalties'] = $this-> hrPenaltyRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['employees'] = $this->hrPenaltyRepository->employees();

        return view('hr::penalties.index', $data);
    }

    /**
     * Show the form for creating a new HrPenalty.
     */
    public function create()
    {
        $data['employees'] = $this->hrPenaltyRepository->employees();
        return view('hr::penalties.create', $data);
    }

    /**
     * Store a newly created HrPenalty in storage.
     */
    public function store(CreateHrPenaltyRequest $request)
    {
        $input = $request->all();

        $penalty = $this->hrPenaltyRepository->create($input);
        $this->hrPenaltyRepository->checkTracking($penalty);

        flash()->success(__('messages.saved', ['model' => __('models/hrPenalties.singular')]));

        return redirect(route('hr.penalties.index'));
    }

    /**
     * Display the specified HrPenalty.
     */
    public function show($id)
    {
        $data['penalty'] = $this->hrPenaltyRepository->find($id);

        if (empty($data['penalty'])) {
            flash()->error(__('models/hrPenalties.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.penalties.index'));
        }

        return view('hr::penalties.show', $data);
    }

    /**
     * Show the form for editing the specified HrPenalty.
     */
    public function edit($id)
    {
        $data['penalty'] = $this->hrPenaltyRepository->find($id);

        $data['employees'] = $this->hrPenaltyRepository->employees();

        if (empty($data['penalty'])) {
            flash()->error(__('models/hrPenalties.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.penalties.index'));
        }

        return view('hr::penalties.edit', $data);
    }

    /**
     * Update the specified HrPenalty in storage.
     */
    public function update($id, UpdateHrPenaltyRequest $request)
    {
        $penalty = $this->hrPenaltyRepository->find($id);

        if (empty($penalty)) {
            flash()->error(__('models/hrPenalties.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.penalties.index'));
        }

        $penalty = $this->hrPenaltyRepository->update($request->all(), $id);
        $this->hrPenaltyRepository->checkTracking($penalty);

        flash()->success(__('messages.updated', ['model' => __('models/hrPenalties.singular')]));

        return redirect(route('hr.penalties.index'));
    }

    /**
     * Remove the specified HrPenalty from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrPenalty = $this->hrPenaltyRepository->find($id);

        if (empty($hrPenalty)) {
            flash()->error(__('models/hrPenalties.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.penalties.index'));
        }

        $this->hrPenaltyRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrPenalties.singular')]));

        return redirect(route('hr.penalties.index'));
    }
}
