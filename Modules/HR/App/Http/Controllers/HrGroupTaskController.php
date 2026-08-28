<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Http\Requests\HrGroupTaskRequest;
use Modules\HR\App\Repositories\HrGroupDetailRepository;
use Modules\HR\App\Repositories\HrGroupRepository;

class HrGroupTaskController extends Controller
{

    private $HrGroupRepository;
    private $HrGroupDetailRepository;

    public function __construct(HrGroupRepository $HrGroupRepository ,HrGroupDetailRepository $HrGroupDetailRepository)
    {
        $this->HrGroupRepository = $HrGroupRepository;
        $this->HrGroupDetailRepository = $HrGroupDetailRepository;
    }

    /**
     * Display a listing of the HrGroupTask.
     */
    public function index(Request $request)
    {
        $data['GroupTasks'] = $this->HrGroupRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['employees'] = $this->HrGroupRepository->employees();
         $data['statuses'] = $this->HrGroupRepository->statuses();


        return view('hr::GroupTask.index', $data);
    }





    /**
     * Show the form for creating a new HrGroupTask.
     */
    public function create()
    {
        $data['employees'] = $this->HrGroupRepository->employees();
        $data['statuses'] = $this->HrGroupRepository->statuses();

        return view('hr::GroupTask.create', $data);
    }

    /**
     * Store a newly created HrGroupTask in storage.
     */
    public function store(HrGroupTaskRequest $request)
    {
        $input = $request->all();
        $hrGroupTask = $this->HrGroupRepository->create($input);
       $this->HrGroupRepository->CreateMember($input ,  $hrGroupTask );
        $hrGroupTask->member =count($hrGroupTask->details) ?? 0;
        $hrGroupTask->save();
        return redirect(route('hr.GroupTask.index'));
    }

    /**
     * Display the specified HrGroupTask.
     */
    public function show($id)
    {
        $data['GroupTask'] = $this->HrGroupRepository->find($id);



        if (empty($data['GroupTask'])) {
            flash()->error(__('hr::models/hr_GroupTask.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.GroupTask.index'));
        }


        return view('hr::GroupTask.show', $data);
    }

    /**
     * Show the form for editing the specified HrGroupTask.
     */
    public function edit($id)
    {
        $data['GroupTask'] = $this->HrGroupRepository->find($id);
        $data['employees'] = $this->HrGroupRepository->employees();

        $data['statuses'] = $this->HrGroupRepository->statuses();




        if (empty($data['GroupTask'])) {
            flash()->error(__('hr::models/hr_GroupTask.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.GroupTask.index'));
        }

        return view('hr::GroupTask.edit', $data);
    }

    /**
     * Update the specified HrGroupTask in storage.
     */
    public function update($id, HrGroupTaskRequest $request)
    {
        $hrGroupTask = $this->HrGroupRepository->find($id);

        if (empty($hrGroupTask)) {
            flash()->error(__('hr::models/hr_GroupTask.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.GroupTask.index'));
        }

        $hrGroupTask = $this->HrGroupRepository->update($request->all(), $id);
        $this->HrGroupRepository->CreateMember($request->all(),  $hrGroupTask );
        $hrGroupTask->member =count($hrGroupTask->details) ?? 0;
        $hrGroupTask->save();

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_GroupTask.singular')]));



        return redirect(route('hr.GroupTask.index'));
    }

    /**
     * Remove the specified HrGroupTask from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrGroupTask = $this->HrGroupRepository->find($id);

        if (empty($hrGroupTask)) {
            flash()->error(__('hr::models/hr_GroupTask.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.GroupTask.index'));
        }

        $this->HrGroupRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_GroupTask.singular')]));
        return redirect(route('hr.GroupTask.index'));
    }













}
