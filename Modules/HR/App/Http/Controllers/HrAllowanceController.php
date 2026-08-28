<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrAllowanceRequest;
use Modules\HR\App\Http\Requests\UpdateHrAllowanceRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrAllowanceRepository;
use Illuminate\Http\Request;


class HrAllowanceController extends AppBaseController
{
    /** @var HrAllowanceRepository $hrAllowanceRepository*/
    private $hrAllowanceRepository;

    public function __construct(HrAllowanceRepository $hrAllowanceRepo)
    {
        $this->hrAllowanceRepository = $hrAllowanceRepo;
    }

    /**
     * Display a listing of the HrAllowance.
     */
    public function index(Request $request)
    {
        $data['allowances'] = $this->hrAllowanceRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrAllowanceRepository->statuses();

        return view('hr::allowances.index', $data);
    }

    /**
     * Show the form for creating a new HrAllowance.
     */
    public function create()
    {
        $data['statuses'] = $this->hrAllowanceRepository->statuses();
        return view('hr::allowances.create', $data);
    }

    /**
     * Store a newly created HrAllowance in storage.
     */
    public function store(CreateHrAllowanceRequest $request)
    {
        $input = $request->all();

        $hrAllowance = $this->hrAllowanceRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_allowances.singular')]));

        return redirect(route('hr.allowances.index'));
    }

    /**
     * Display the specified HrAllowance.
     */
    public function show($id)
    {
        $hrAllowance = $this->hrAllowanceRepository->find($id);

        if (empty($hrAllowance)) {
            flash()->error(__('hr::models/hr_allowances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.allowances.index'));
        }

        return view('hr::allowances.show')->with('hrAllowance', $hrAllowance);
    }

    /**
     * Show the form for editing the specified HrAllowance.
     */
    public function edit($id)
    {
        $data['allowance'] = $this->hrAllowanceRepository->find($id);

        if (empty($data['allowance'])) {
            flash()->error(__('hr::models/hr_allowances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.allowances.index'));
        }
        $data['statuses'] = $this->hrAllowanceRepository->statuses();

        return view('hr::allowances.edit', $data);
    }

    /**
     * Update the specified HrAllowance in storage.
     */
    public function update($id, UpdateHrAllowanceRequest $request)
    {
        $hrAllowance = $this->hrAllowanceRepository->find($id);

        if (empty($hrAllowance)) {
            flash()->error(__('hr::models/hr_allowances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.allowances.index'));
        }

        $hrAllowance = $this->hrAllowanceRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_allowances.singular')]));

        return redirect(route('hr.allowances.index'));
    }

    /**
     * Remove the specified HrAllowance from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrAllowance = $this->hrAllowanceRepository->find($id);

        if (empty($hrAllowance) || $hrAllowance->HrSalaryAllowance()->count() > 0) {
            flash()->error(__('hr::models/hr_allowances.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.allowances.index'));
        }

        $this->hrAllowanceRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_allowances.singular')]));

        return redirect(route('hr.allowances.index'));
    }
}
