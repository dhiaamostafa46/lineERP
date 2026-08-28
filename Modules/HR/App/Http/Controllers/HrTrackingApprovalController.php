<?php

namespace Modules\HR\App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrTrackingApprovalRepository;
use Modules\HR\App\Http\Requests\CreateHrTrackingApprovalRequest;
use Modules\HR\App\Http\Requests\UpdateHrTrackingApprovalRequest;

class HrTrackingApprovalController extends AppBaseController
{
    /** @var HrTrackingApprovalRepository $hrTrackingApprovalsRepository*/
    private $hrTrackingApprovalsRepository;

    public function __construct(HrTrackingApprovalRepository $hrTrackingApprovalsRepo)
    {
        $this->hrTrackingApprovalsRepository = $hrTrackingApprovalsRepo;
    }

    /**
     * Display a listing of the HrTrackingApproval.
     */
    public function index(Request $request)
    {
        $data['tracking_approvals'] = $this->hrTrackingApprovalsRepository->paginate(10);

        return view('hr::tracking_approvals.index', $data);
    }

    /**
     * Show the form for creating a new HrTrackingApproval.
     */
    public function create()
    {
        return view('hr::tracking_approvals.create');
    }

    /**
     * Store a newly created HrTrackingApproval in storage.
     */
    public function store(CreateHrTrackingApprovalRequest $request)
    {
        $input = $request->all();

        $tracking_approval = $this->hrTrackingApprovalsRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrTrackingApprovals.singular')]));

        return redirect(route('hr.tracking-approvals.index'));
    }

    /**
     * Display the specified HrTrackingApproval.
     */
    public function show($id)
    {
        $data['tracking_approval'] = $this->hrTrackingApprovalsRepository->find($id);

        if (empty($data['tracking_approval'])) {
            flash()->error(__('models/hrTrackingApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-approvals.index'));
        }

        return view('hr::tracking_approvals.show', $data);
    }

    /**
     * Show the form for editing the specified HrTrackingApproval.
     */
    public function edit($id)
    {
        $data['tracking_approval'] = $this->hrTrackingApprovalsRepository->find($id);

        if (empty($data['tracking_approval'])) {
            flash()->error(__('models/hrTrackingApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-approvals.index'));
        }

        return view('hr::tracking_approvals.edit', $data);
    }

    /**
     * Update the specified HrTrackingApproval in storage.
     */
    public function update($id, UpdateHrTrackingApprovalRequest $request)
    {
        $tracking_approval = $this->hrTrackingApprovalsRepository->find($id);

        if (empty($tracking_approval)) {
            flash()->error(__('models/hrTrackingApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-approvals.index'));
        }

        $tracking_approval = $this->hrTrackingApprovalsRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrTrackingApprovals.singular')]));

        return redirect(route('hr.tracking-approvals.index'));
    }

    /**
     * Remove the specified HrTrackingApproval from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $tracking_approval = $this->hrTrackingApprovalsRepository->find($id);

        if (empty($tracking_approval)) {
            flash()->error(__('models/hrTrackingApprovals.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.tracking-approvals.index'));
        }

        $this->hrTrackingApprovalsRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrTrackingApprovals.singular')]));

        return redirect(route('hr.tracking-approvals.index'));
    }
}
