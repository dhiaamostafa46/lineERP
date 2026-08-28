<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrContractRequest;
use Modules\HR\App\Http\Requests\UpdateHrContractRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Organization;
use Modules\HR\App\Repositories\HrContractRepository;
use Illuminate\Http\Request;


class HrContractController extends AppBaseController
{
    /** @var HrContractRepository $hrContractRepository*/
    private $hrContractRepository;

    public function __construct(HrContractRepository $hrContractRepo)
    {
        $this->hrContractRepository = $hrContractRepo;
    }

    /**
     * Display a listing of the HrContract.
     */
    public function index(Request $request)
    {
        $data['contracts'] = $this->hrContractRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrContractRepository->statuses();
        $data['types'] = $this->hrContractRepository->types();
        $data['employees'] = $this->hrContractRepository->employees();

        return view('hr::contracts.index', $data);
    }

    /**
     * Show the form for creating a new HrContract.
     */
    public function create()
    {
        $data['employees'] = $this->hrContractRepository->employees();
        $data['statuses'] = $this->hrContractRepository->statuses();
        $data['types'] = $this->hrContractRepository->types();
        $data['qiwas'] = $this->hrContractRepository->qiwas();
        return view('hr::contracts.create', $data);
    }

    /**
     * Store a newly created HrContract in storage.
     */
    public function store(CreateHrContractRequest $request)
    {
        $input = $request->all();

        $contract = $this->hrContractRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_contracts.singular')]));

        return redirect(route('hr.contracts.index'));
    }

    /**
     * Display the specified HrContract.
     */
    public function show($id)
    {
        $data['contract'] = $this->hrContractRepository->find($id);
        $data['Organization'] = Organization::first();


        if (empty($data['contract'])) {
            flash()->error(__('hr::models/hr_contracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contracts.index'));
        }

        return view('hr::contracts.show', $data);
    }

    /**
     * Show the form for editing the specified HrContract.
     */
    public function edit($id)
    {
        $data['contract'] = $this->hrContractRepository->find($id);



        if (empty($data['contract'])) {
            flash()->error(__('hr::models/hr_contracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contracts.index'));
        }
        $data['statuses'] = $this->hrContractRepository->statuses();
        $data['types'] = $this->hrContractRepository->types();
        $data['employees'] = $this->hrContractRepository->employees();
        $data['qiwas'] = $this->hrContractRepository->qiwas();

        return view('hr::contracts.edit', $data);
    }

    /**
     * Update the specified HrContract in storage.
     */
    public function update($id, UpdateHrContractRequest $request)
    {
        $contract = $this->hrContractRepository->find($id);

        if (empty($contract)) {
            flash()->error(__('hr::models/hr_contracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contracts.index'));
        }

        $contract = $this->hrContractRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_contracts.singular')]));

        return redirect(route('hr.contracts.index'));
    }

    /**
     * Remove the specified HrContract from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $contract = $this->hrContractRepository->find($id);

        if (empty($contract)) {
            flash()->error(__('hr::models/hr_contracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contracts.index'));
        }

        $this->hrContractRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_contracts.singular')]));

        return redirect(route('hr.contracts.index'));
    }
}
