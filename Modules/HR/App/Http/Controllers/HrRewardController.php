<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrRewardRequest;
use Modules\HR\App\Http\Requests\UpdateHrRewardRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrRewardRepository;
use Illuminate\Http\Request;

class HrRewardController extends AppBaseController
{
    /** @var HrRewardRepository $hrRewardRepository*/
    private $hrRewardRepository;

    public function __construct(HrRewardRepository $hrRewardRepo)
    {
        $this->hrRewardRepository = $hrRewardRepo;
    }

    /**
     * Display a listing of the HrReward.
     */
    public function index(Request $request)
    {
        $data['rewards'] = $this->hrRewardRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['employees'] = $this->hrRewardRepository->employees();
        $data['types'] = $this->hrRewardRepository->types();
        $data['statuses'] = $this->hrRewardRepository->statuses();
        return view('hr::rewards.index', $data);
    }

    /**
     * Show the form for creating a new HrReward.
     */
    public function create()
    {
        $data['employees'] = $this->hrRewardRepository->employees();
        $data['types'] = $this->hrRewardRepository->types();
        $data['statuses'] = $this->hrRewardRepository->statuses();

        return view('hr::rewards.create', $data);
    }

    /**
     * Store a newly created HrReward in storage.
     */
    public function store(CreateHrRewardRequest $request)
    {

       
        $input = $request->all();

        $reward = $this->hrRewardRepository->create($input);
        $this->hrRewardRepository->checkTracking($reward);

        flash()->success(__('messages.saved', ['model' => __('models/hr_rewards.singular')]));

        return redirect(route('hr.rewards.index'));
    }

    /**
     * Display the specified HrReward.
     */
    public function show($id)
    {
        $data['reward'] = $this->hrRewardRepository->find($id);

        if (empty($data['reward'])) {
            flash()->error(__('models/hr_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.rewards.index'));
        }

        return view('hr::rewards.show', $data);
    }

    /**
     * Show the form for editing the specified HrReward.
     */
    public function edit($id)
    {
        $data['reward'] = $this->hrRewardRepository->find($id);
        if (empty($data['reward'])) {
            flash()->error(__('models/hr_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.rewards.index'));
        }
        $data['employees'] = $this->hrRewardRepository->employees();
        $data['types'] = $this->hrRewardRepository->types();
        $data['statuses'] = $this->hrRewardRepository->statuses();

        return view('hr::rewards.edit', $data);
    }

    /**
     * Update the specified HrReward in storage.
     */
    public function update($id, UpdateHrRewardRequest $request)
    {
        $reward = $this->hrRewardRepository->find($id);

        if (empty($reward)) {
            flash()->error(__('models/hr_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.rewards.index'));
        }

        $reward = $this->hrRewardRepository->update($request->all(), $id);
        $this->hrRewardRepository->checkTracking($reward);

        flash()->success(__('messages.updated', ['model' => __('models/hr_rewards.singular')]));

        return redirect(route('hr.rewards.index'));
    }

    /**
     * Remove the specified HrReward from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $reward = $this->hrRewardRepository->find($id);

        if (empty($reward)) {
            flash()->error(__('models/hr_rewards.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.rewards.index'));
        }

        $this->hrRewardRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hr_rewards.singular')]));

        return redirect(route('hr.rewards.index'));
    }
}
