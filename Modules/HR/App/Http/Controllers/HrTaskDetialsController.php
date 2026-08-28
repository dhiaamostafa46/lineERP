<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Http\Requests\HrTaskDetailsRequest;
use Modules\HR\App\Repositories\HrTaskDetailsRepository;

class HrTaskDetialsController extends Controller
{
    private $HrTaskDetailsRepository;

    public function __construct(HrTaskDetailsRepository $HrTaskDetailsRepository)
    {
        $this->HrTaskDetailsRepository = $HrTaskDetailsRepository;
    }

    /**
     * Display a listing of the HrTasts.
     */
    public function index(Request $request)
    {
        // $data['Tasks'] = $this->HrTaskDetailsRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        // $data['employees'] = $this->HrTaskDetailsRepository->employees();
        // $data['statuses'] = $this->HrTaskDetailsRepository->statuses();

        // return view('hr::Tasts.index', $data);
    }

    /**
     * Show the form for creating a new HrTasts.
     */
    public function create()
    {
        // $data['employees'] = $this->HrTaskDetailsRepository->employees();
        // $data['statuses'] = $this->HrTaskDetailsRepository->statuses();

        // return view('hr::Tasts.create', $data);
    }

    /**
     * Store a newly created HrTasts in storage.
     */
    public function store(HrTaskDetailsRequest $request)
    {
        $input = $request->all();
        $hrTasts = $this->HrTaskDetailsRepository->create($input);
        return redirect()->back();
    }

    /**
     * Display the specified HrTasts.
     */
    public function show($id)
    {
        // $data['Tasts'] = $this->HrTaskDetailsRepository->find($id);

        // if (empty($data['Tasts'])) {
        //     flash()->error(__('hr::models/hr_tasts.singular') . ' ' . __('messages.not_found'));
        //     return redirect(route('hr.Task.index'));
        // }

        // return view('hr::Tasts.show', $data);
    }

    /**
     * Show the form for editing the specified HrTasts.
     */
    public function edit($id)
    {
        // $data['Tasts'] = $this->HrTaskDetailsRepository->find($id);
        // $data['employees'] = $this->HrTaskDetailsRepository->employees();
        // $data['statuses'] = $this->HrTaskDetailsRepository->statuses();


        // if (empty($data['Tasts'])) {
        //     flash()->error(__('hr::models/hr_tasts.singular') . ' ' . __('messages.not_found'));
        //     return redirect(route('hr.Task.index'));
        // }

        // $data['statuses'] = $this->HrTaskDetailsRepository->statuses();
        // return view('hr::Tasts.edit', $data);
    }

    /**
     * Update the specified HrTasts in storage.
     */
    public function update($id, HrTaskDetailsRequest $request)
    {
        $hrTasts = $this->HrTaskDetailsRepository->find($id);

        if (empty($hrTasts)) {
            flash()->error(__('hr::models/hr_tasts.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.Task.index'));
        }

        $hrTasts = $this->HrTaskDetailsRepository->update($request->all(), $id);
        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_tasts.singular')]));

        return redirect(route('hr.Task.index'));
    }

    /**
     * Remove the specified HrTasts from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        // $hrTasts = $this->HrTaskDetailsRepository->find($id);

        // if (empty($hrTasts)) {
        //     flash()->error(__('hr::models/hr_tasts.singular') . ' ' . __('messages.not_found'));
        //     return redirect(route('hr.Task.index'));
        // }

        // $this->HrTaskDetailsRepository->delete($id);

        // flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_tasts.singular')]));
        // return redirect(route('hr.Task.index'));
    }

}
