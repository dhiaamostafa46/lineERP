<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrPayrollApprovalRequest;
use Modules\HR\App\Http\Requests\UpdateHrPayrollApprovalRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrPayrollApprovalRepository;
use Illuminate\Http\Request;


class HrPayrollApprovalController extends AppBaseController
{
    /** @var HrPayrollApprovalRepository $hrPayrollApprovalRepository*/
    private $hrPayrollApprovalRepository;

    public function __construct(HrPayrollApprovalRepository $hrPayrollApprovalRepo)
    {
        $this->hrPayrollApprovalRepository = $hrPayrollApprovalRepo;
    }

    /**
     * Display a listing of the HrPayrollApproval.
     */
    public function index(Request $request)
    {
        $hrPayrollApprovals = $this->hrPayrollApprovalRepository->paginate(10);

        return view('hr::payroll_approvals.index')
            ->with('hrPayrollApprovals', $hrPayrollApprovals);
    }

    /**
     * Show the form for creating a new HrPayrollApproval.
     */
    public function create()
    {
        return view('hr::payroll_approvals.create');
    }

    /**
     * Store a newly created HrPayrollApproval in storage.
     */
    public function store(CreateHrPayrollApprovalRequest $request)
    {
        $input = $request->all();

        $hrPayrollApproval = $this->hrPayrollApprovalRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrPayrollApprovals.singular')]));

        return redirect(route('hrPayrollApprovals.index'));
    }

    /**
     * Display the specified HrPayrollApproval.
     */
    public function show($id)
    {
        $hrPayrollApproval = $this->hrPayrollApprovalRepository->find($id);

        if (empty($hrPayrollApproval)) {
            flash()->error(__('models/hrPayrollApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollApprovals.index'));
        }

        return view('hr::payroll_approvals.show')->with('hrPayrollApproval', $hrPayrollApproval);
    }

    /**
     * Show the form for editing the specified HrPayrollApproval.
     */
    public function edit($id)
    {
        $hrPayrollApproval = $this->hrPayrollApprovalRepository->find($id);

        if (empty($hrPayrollApproval)) {
            flash()->error(__('models/hrPayrollApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollApprovals.index'));
        }

        return view('hr::payroll_approvals.edit')->with('hrPayrollApproval', $hrPayrollApproval);
    }

    /**
     * Update the specified HrPayrollApproval in storage.
     */
    public function update($id, UpdateHrPayrollApprovalRequest $request)
    {
        $hrPayrollApproval = $this->hrPayrollApprovalRepository->find($id);

        if (empty($hrPayrollApproval)) {
            flash()->error(__('models/hrPayrollApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollApprovals.index'));
        }

        $hrPayrollApproval = $this->hrPayrollApprovalRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrPayrollApprovals.singular')]));

        return redirect(route('hrPayrollApprovals.index'));
    }

    /**
     * Remove the specified HrPayrollApproval from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrPayrollApproval = $this->hrPayrollApprovalRepository->find($id);

        if (empty($hrPayrollApproval)) {
            flash()->error(__('models/hrPayrollApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hrPayrollApprovals.index'));
        }

        $this->hrPayrollApprovalRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrPayrollApprovals.singular')]));

        return redirect(route('hrPayrollApprovals.index'));
    }
}
