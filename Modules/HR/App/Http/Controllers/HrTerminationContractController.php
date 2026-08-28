<?php

namespace Modules\HR\App\Http\Controllers;

use  Modules\HR\App\Http\Requests\CreateHrTerminationContractRequest;
use  Modules\HR\App\Http\Requests\UpdateHrTerminationContractRequest;
use  App\Http\Controllers\AppBaseController;
use  Modules\HR\App\Repositories\HrTerminationContractRepository;
use Illuminate\Http\Request;


class HrTerminationContractController extends AppBaseController
{
    /** @var HrTerminationContractRepository $hrTerminationContractRepository*/
    private $hrTerminationContractRepository;

    public function __construct(HrTerminationContractRepository $hrTerminationContractRepo)
    {
        $this->hrTerminationContractRepository = $hrTerminationContractRepo;
    }

    /**
     * Display a listing of the HrTerminationContract.
     */
    public function index(Request $request)
    {
        $data['termination_contracts'] = $this->hrTerminationContractRepository->paginate(10);
        return view('hr::termination_contracts.index', $data);
    }

    /**
     * Show the form for creating a new HrTerminationContract.
     */
    public function create()
    {
        return view('hr::termination_contracts.create');
    }

    /**
     * Store a newly created HrTerminationContract in storage.
     */
    public function store(CreateHrTerminationContractRequest $request)
    {
        $input = $request->all();

        $termination_contract = $this->hrTerminationContractRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrTerminationContracts.singular')]));

        return redirect(route('hr.termination-contracts.index'));
    }

    /**
     * Display the specified HrTerminationContract.
     */
    public function show($id)
    {
        $data['termination_contract'] = $this->hrTerminationContractRepository->find($id);

        if (empty($data['termination_contract'])) {
            flash()->error(__('models/hrTerminationContracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-contracts.index'));
        }

        return view('hr::termination_contracts.show', $data);
    }

    /**
     * Show the form for editing the specified HrTerminationContract.
     */
    public function edit($id)
    {
        $data['termination_contract'] = $this->hrTerminationContractRepository->find($id);

        if (empty($data['termination_contract'])) {
            flash()->error(__('models/hrTerminationContracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-contracts.index'));
        }

        return view('hr::termination_contracts.edit', $data);
    }

    /**
     * Update the specified HrTerminationContract in storage.
     */
    public function update($id, UpdateHrTerminationContractRequest $request)
    {
        $termination_contract = $this->hrTerminationContractRepository->find($id);

        if (empty($termination_contract)) {
            flash()->error(__('models/hrTerminationContracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-contracts.index'));
        }

        $termination_contract = $this->hrTerminationContractRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrTerminationContracts.singular')]));

        return redirect(route('hr.termination-contracts.index'));
    }

    /**
     * Remove the specified HrTerminationContract from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $termination_contract = $this->hrTerminationContractRepository->find($id);

        if (empty($termination_contract)) {
            flash()->error(__('models/hrTerminationContracts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-contracts.index'));
        }

        $this->hrTerminationContractRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrTerminationContracts.singular')]));

        return redirect(route('hr.termination-contracts.index'));
    }
}
