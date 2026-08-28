<?php

namespace Modules\HR\App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Models\HrTerminationTypeReward;
use Modules\HR\App\Repositories\HrTerminationTypeRewardRepository;
use Modules\HR\App\Http\Requests\CreateHrTerminationTypeRewardRequest;
use Modules\HR\App\Http\Requests\UpdateHrTerminationTypeRewardRequest;

class HrTerminationTypeRewardController extends AppBaseController
{
    /** @var HrTerminationTypeRewardRepository $hrTerminationTypeRewardRepository*/
    private $hrTerminationTypeRewardRepository;

    public function __construct(HrTerminationTypeRewardRepository $hrTerminationTypeRewardRepo)
    {
        $this->hrTerminationTypeRewardRepository = $hrTerminationTypeRewardRepo;
    }

    /**
     * Display a listing of the HrTerminationTypeReward.
     */
    public function index(Request $request)
    {
        $data['termination_type_rewards'] = $this->hrTerminationTypeRewardRepository->paginate(10);

        return view('hr::termination_type_rewards.index', $data);
    }

    /**
     * Show the form for creating a new HrTerminationTypeReward.
     */
    public function create()
    {
        return view('hr::termination_type_rewards.create');
    }

    /**
     * Store a newly created HrTerminationTypeReward in storage.
     */
    public function store(CreateHrTerminationTypeRewardRequest $request)
    {
        $input = $request->all();

        $termination_type_reward = $this->hrTerminationTypeRewardRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr:;models/hr_termination_type_rewards.singular')]));

        return redirect(route('hr.termination-type-rewards.index'));
    }

    /**
     * Display the specified HrTerminationTypeReward.
     */
    public function show($id)
    {
        $data['termination_type_reward'] = $this->hrTerminationTypeRewardRepository->find($id);

        if (empty($data['termination_type_reward'])) {
            flash()->error(__('hr:;models/hr_termination_type_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-type-rewards.index'));
        }

        return view('hr::termination_type_rewards.show', $data);
    }

    /**
     * Show the form for editing the specified HrTerminationTypeReward.
     */
    public function edit($id)
    {
        $data['termination_type_reward'] = $this->hrTerminationTypeRewardRepository->find($id);

        if (empty($data['termination_type_reward'])) {
            flash()->error(__('hr:;models/hr_termination_type_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-type-rewards.index'));
        }

        return view('hr::termination_type_rewards.edit', $data);
    }

    /**
     * Update the specified HrTerminationTypeReward in storage.
     */
    public function update($id, UpdateHrTerminationTypeRewardRequest $request)
    {
        $termination_type_reward = $this->hrTerminationTypeRewardRepository->find($id);

        if (empty($termination_type_reward)) {
            flash()->error(__('hr:;models/hr_termination_type_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-type-rewards.index'));
        }

        $termination_type_reward = $this->hrTerminationTypeRewardRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr:;models/hr_termination_type_rewards.singular')]));

        return redirect(route('hr.termination-type-rewards.index'));
    }

    /**
     * Remove the specified HrTerminationTypeReward from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $termination_type_reward = $this->hrTerminationTypeRewardRepository->find($id);

        if (empty($termination_type_reward)) {
            flash()->error(__('hr:;models/hr_termination_type_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.termination-type-rewards.index'));
        }

        $this->hrTerminationTypeRewardRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr:;models/hr_termination_type_rewards.singular')]));

        return redirect(route('hr.termination-type-rewards.index'));
    }
}
