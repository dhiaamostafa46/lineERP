<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrContractTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrContractTypeRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrContractTypeRepository;
use Illuminate\Http\Request;


class HrContractTypeController extends AppBaseController
{
    /** @var HrContractTypeRepository $hrContractTypeRepository*/
    private $hrContractTypeRepository;

    public function __construct(HrContractTypeRepository $hrContractTypeRepo)
    {
        $this->hrContractTypeRepository = $hrContractTypeRepo;
    }

    /**
     * Display a listing of the HrContractType.
     */
    public function index(Request $request)
    {
        $data['contract_types'] = $this->hrContractTypeRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrContractTypeRepository->statuses();
        return view('hr::contract_types.index', $data);
    }

    /**
     * Show the form for creating a new HrContractType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrContractTypeRepository->statuses();

        return view('hr::contract_types.create', $data);
    }

    /**
     * Store a newly created HrContractType in storage.
     */
    public function store(CreateHrContractTypeRequest $request)
    {
        $input = $request->all();

        $contract_type = $this->hrContractTypeRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_contractTypes.singular')]));

        return redirect(route('hr.contract_types.index'));
    }

    /**
     * Display the specified HrContractType.
     */
    public function show($id)
    {
        $data['contract_type'] = $this->hrContractTypeRepository->find($id);

        if (empty($data['contract_type'])) {
            flash()->error(__('hr::models/hr_contractTypes.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contract_types.index'));
        }

        return view('hr::contract_types.show', $data);
    }

    /**
     * Show the form for editing the specified HrContractType.
     */
    public function edit($id)
    {
        $data['contract_type'] = $this->hrContractTypeRepository->find($id);

        if (empty($data['contract_type'])) {
            flash()->error(__('hr::models/hr_contractTypes.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contract_types.index'));
        }

        $data['statuses'] = $this->hrContractTypeRepository->statuses();
        return view('hr::contract_types.edit', $data);
    }

    /**
     * Update the specified HrContractType in storage.
     */
    public function update($id, UpdateHrContractTypeRequest $request)
    {
        $contract_type = $this->hrContractTypeRepository->find($id);

        if (empty($contract_type)) {
            flash()->error(__('hr::models/hr_contractTypes.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contract_types.index'));
        }

        $contract_type = $this->hrContractTypeRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_contractTypes.singular')]));

        return redirect(route('hr.contract_types.index'));
    }

    /**
     * Remove the specified HrContractType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $contract_type = $this->hrContractTypeRepository->find($id);

        if (empty($contract_type)) {
            flash()->error(__('hr::models/hr_contractTypes.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.contract_types.index'));
        }

        $this->hrContractTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_contractTypes.singular')]));

        return redirect(route('hr.contract_types.index'));
    }
}
