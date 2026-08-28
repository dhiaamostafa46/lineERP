<?php

namespace Modules\HR\App\Http\Controllers;


use Illuminate\Http\Request;
use Modules\HR\App\Models\HrTermination;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrTerminationRepository;
use Modules\HR\App\Http\Requests\CreateHrTerminationRequest;
use Modules\HR\App\Http\Requests\UpdateHrTerminationRequest;

class HrTerminationController extends AppBaseController
{
    /** @var HrTerminationRepository $hrTerminationRepository*/
    private $hrTerminationRepository;

    public function __construct(HrTerminationRepository $hrTerminationRepo)
    {
        $this->hrTerminationRepository = $hrTerminationRepo;
    }

    /**
     * Display a listing of the HrTermination.
     */
    public function index(Request $request)
    {
        $data['terminations'] = $this->hrTerminationRepository->paginate(10);
        return view('hr::terminations.index', $data);
    }

    /**
     * Show the form for creating a new HrTermination.
     */
    public function create()
    {
        return view('hr::terminations.create');
    }

    /**
     * Store a newly created HrTermination in storage.
     */
    public function store(CreateHrTerminationRequest $request)
    {
        $input = $request->all();

        $termination = $this->hrTerminationRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrTerminations.singular')]));

        return redirect(route('hr.terminations.index'));
    }

    /**
     * Display the specified HrTermination.
     */
    public function show($id)
    {
        $data['termination'] = $this->hrTerminationRepository->find($id);

        if (empty($data['termination'])) {
            flash()->error(__('models/hrTerminations.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.terminations.index'));
        }

        return view('hr::terminations.show', $data);
    }

    /**
     * Show the form for editing the specified HrTermination.
     */
    public function edit($id)
    {
        $data['termination'] = $this->hrTerminationRepository->find($id);

        if (empty($data['termination'])) {
            flash()->error(__('models/hrTerminations.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.terminations.index'));
        }

        return view('hr::terminations.edit', $data);
    }

    /**
     * Update the specified HrTermination in storage.
     */
    public function update($id, UpdateHrTerminationRequest $request)
    {
        $termination = $this->hrTerminationRepository->find($id);

        if (empty($termination)) {
            flash()->error(__('models/hrTerminations.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.terminations.index'));
        }

        $termination = $this->hrTerminationRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hrTerminations.singular')]));

        return redirect(route('hr.terminations.index'));
    }

    /**
     * Remove the specified HrTermination from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $termination = $this->hrTerminationRepository->find($id);

        if (empty($termination)) {
            flash()->error(__('models/hrTerminations.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.terminations.index'));
        }

        $this->hrTerminationRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrTerminations.singular')]));

        return redirect(route('hr.terminations.index'));
    }
}
