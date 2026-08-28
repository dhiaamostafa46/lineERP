<?php

namespace Modules\HR\App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Models\HrTerminationType;
use Modules\HR\App\Repositories\HrTerminationTypeRepository;
use Modules\HR\App\Http\Requests\CreateHrTerminationTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrTerminationTypeRequest;

class HrTerminationTypeController extends AppBaseController
{
    /** @var HrTerminationTypeRepository $hrTerminationTypeRepository*/
    private $hrTerminationTypeRepository;

    public function __construct(HrTerminationTypeRepository $hrTerminationTypeRepo)
    {
        $this->hrTerminationTypeRepository = $hrTerminationTypeRepo;
    }

    /**
     * Display a listing of the HrTerminationType.
     */
    public function index(Request $request)
    {
        $data['termination_types'] = $this->hrTerminationTypeRepository->allQuery($request->all())->paginate(10);
        $data['statuses'] = $this->hrTerminationTypeRepository->statuses();

        return view('hr::termination_types.index', $data);
    }

    /**
     * Show the form for creating a new HrTerminationType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrTerminationTypeRepository->statuses();

        return view('hr::termination_types.create', $data);
    }

    /**
     * Store a newly created HrTerminationType in storage.
     */
    public function store(CreateHrTerminationTypeRequest $request)
    {
        $input = $request->all();
        $termination_type = $this->hrTerminationTypeRepository->create($input);
        $termination_type->rewards()->createMany($input['rewards']);

        flash()->success(__('messages.saved', ['model' => __('hr:;models/hr_termination_types.singular')]));

        return redirect(route('hr.termination_types.index'));
    }

    /**
     * Display the specified HrTerminationType.
     */
    public function show($id)
    {
        $data['termination_type'] = $this->hrTerminationTypeRepository->find($id);

        if (empty($data['termination_type'])) {
            flash()->error(__('hr:;models/hr_termination_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination_types.index'));
        }

        return view('hr::termination_types.show', $data);
    }

    /**
     * Show the form for editing the specified HrTerminationType.
     */
    public function edit($id)
    {
        $data['termination_type'] = $this->hrTerminationTypeRepository->find($id);

        if (empty($data['termination_type'])) {
            flash()->error(__('hr:;models/hr_termination_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination_types.index'));
        }
        $data['statuses'] = $this->hrTerminationTypeRepository->statuses();

        return view('hr::termination_types.edit', $data);
    }

    /**
     * Update the specified HrTerminationType in storage.
     */
    public function update($id, UpdateHrTerminationTypeRequest $request)
    {
        $termination_type = $this->hrTerminationTypeRepository->find($id);

        if (empty($termination_type)) {
            flash()->error(__('hr:;models/hr_termination_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination_types.index'));
        }

        $termination_type = $this->hrTerminationTypeRepository->update($request->all(), $id);
        $termination_type->rewards()->delete();
        $termination_type->rewards()->createMany($request->rewards);
        flash()->success(__('messages.updated', ['model' => __('hr:;models/hr_termination_types.singular')]));

        return redirect(route('hr.termination_types.index'));
    }

    /**
     * Remove the specified HrTerminationType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $termination_type = $this->hrTerminationTypeRepository->find($id);

        if (empty($termination_type)) {
            flash()->error(__('hr:;models/hr_termination_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination_types.index'));
        }

        $this->hrTerminationTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr:;models/hr_termination_types.singular')]));

        return redirect(route('hr.termination_types.index'));
    }
}
