<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Http\Requests\HrTaskRequest;
use Modules\HR\App\Repositories\HrTaskRepository;


class HrTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $HrTaskRepository;

    public function __construct(HrTaskRepository $HrTaskRepository)
    {
        $this->HrTaskRepository = $HrTaskRepository;
    }

    /**
     * Display a listing of the HrTasts.
     */
    public function index(Request $request)
    {
        $data['Tasks'] = $this->HrTaskRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['employees'] = $this->HrTaskRepository->employees();
        $data['statuses'] = $this->HrTaskRepository->statuses();


        return view('hr::Task.index', $data);
    }

    /**
     * Show the form for creating a new HrTasts.
     */
    public function create()
    {
        $data['employees'] = $this->HrTaskRepository->employees();
        $data['Department'] = $this->HrTaskRepository->Department();
        $data['Group'] = $this->HrTaskRepository->Group();
        $data['statuses'] = $this->HrTaskRepository->statuses();
        $data['flages'] = $this->HrTaskRepository->flages();





        return view('hr::Task.create', $data);
    }

    /**
     * Store a newly created HrTasts in storage.
     */
    public function store(HrTaskRequest $request)
    {
        $input = $request->all();
        $hrTasts = $this->HrTaskRepository->create($input);



        if( $hrTasts->status ==4 || $hrTasts->status ==3){
           $this->HrTaskRepository->Done( $hrTasts );
        }
        return redirect(route('hr.Task.index'));
    }

    /**
     * Display the specified HrTasts.
     */
    public function show($id)
    {
        $data['Tasts'] = $this->HrTaskRepository->find($id);


        if (empty($data['Tasts'])) {
            flash()->error(__('hr::models/hr_tasks.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.Task.index'));
        }
        $data['TastDetails'] = $data['Tasts']->HrTaskdetails;

        return view('hr::Task.show', $data);
    }

    public function showTask($id)
    {
        $data['Tasts'] = $this->HrTaskRepository->find($id);


        if (empty($data['Tasts'])) {
            flash()->error(__('hr::models/hr_tasks.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.Task.index'));
        }
        $data['TastDetails'] = $data['Tasts']->HrTaskdetails;
        $data['statuses'] = $this->HrTaskRepository->statuses();


        return view('hr::Task.showEmp', $data);
    }

    /**
     * Show the form for editing the specified HrTasts.
     */
    public function edit($id)
    {
        $data['task'] = $this->HrTaskRepository->find($id);
        $data['employees'] = $this->HrTaskRepository->employees();
        $data['Department'] = $this->HrTaskRepository->Department();
        $data['Group'] = $this->HrTaskRepository->Group();
        $data['statuses'] = $this->HrTaskRepository->statuses();
        $data['flages'] = $this->HrTaskRepository->flages();


        if (empty($data['task'])) {
            flash()->error(__('hr::models/hr_tasks.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.Task.index'));
        }

        $data['statuses'] = $this->HrTaskRepository->statuses();
        return view('hr::Task.edit', $data);
    }

    /**
     * Update the specified HrTasts in storage.
     */
    public function update($id, HrTaskRequest $request)
    {


        $hrTasts = $this->HrTaskRepository->find($id);
        // dd($request->all());
        if (empty($hrTasts)) {
            flash()->error(__('hr::models/hr_tasks.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.Task.index'));
        }

        $hrTasts = $this->HrTaskRepository->update($request->all(), $id);
        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_tasks.singular')]));

        if( $hrTasts->status ==4 || $hrTasts->status ==3){
            $this->HrTaskRepository->Done( $hrTasts );
         }

        return redirect()->back();

    }

    /**
     * Remove the specified HrTasts from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrTasts = $this->HrTaskRepository->find($id);

        if (empty($hrTasts)) {
            flash()->error(__('hr::models/hr_tasts.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.Task.index'));
        }

        $this->HrTaskRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_tasts.singular')]));
        return redirect(route('hr.Task.index'));
    }
}
