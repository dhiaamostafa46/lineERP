<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrDeductRequest;
use Modules\HR\App\Http\Requests\UpdateHrDeductRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrDeductRepository;
use Illuminate\Http\Request;


class HrDeductController extends AppBaseController
{
    /** @var HrDeductRepository $hrDeductRepository*/
    private $hrDeductRepository;

    public function __construct(HrDeductRepository $hrDeductRepo)
    {
        $this->hrDeductRepository = $hrDeductRepo;
    }

    /**
     * Display a listing of the HrDeduct.
     */
    public function index(Request $request)
    {
        $data['deducts'] = $this->hrDeductRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrDeductRepository->statuses();
        return view('hr::deducts.index', $data);
    }

    /**
     * Show the form for creating a new HrDeduct.
     */
    public function create()
    {
        $data['statuses'] = $this->hrDeductRepository->statuses();

        return view('hr::deducts.create', $data);
    }

    /**
     * Store a newly created HrDeduct in storage.
     */
    public function store(CreateHrDeductRequest $request)
    {
        $input = $request->all();

        $deduct = $this->hrDeductRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_deducts.singular')]));

        return redirect(route('hr.deducts.index'));
    }

    /**
     * Display the specified HrDeduct.
     */
    public function show($id)
    {
        $deduct = $this->hrDeductRepository->find($id);

        if (empty($deduct)) {
            flash()->error(__('hr::models/hr_deducts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.deducts.index'));
        }

        return view('hr::deducts.show')->with('hrDeduct', $deduct);
    }

    /**
     * Show the form for editing the specified HrDeduct.
     */
    public function edit($id)
    {
        $data['deduct'] = $this->hrDeductRepository->find($id);

        if (empty($data['deduct'])) {
            flash()->error(__('hr::models/hr_deducts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.deducts.index'));
        }
        $data['statuses'] = $this->hrDeductRepository->statuses();

        return view('hr::deducts.edit', $data);
    }

    /**
     * Update the specified HrDeduct in storage.
     */
    public function update($id, UpdateHrDeductRequest $request)
    {
        $deduct = $this->hrDeductRepository->find($id);

        if (empty($deduct)) {
            flash()->error(__('hr::models/hr_deducts.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.deducts.index'));
        }

        $deduct = $this->hrDeductRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_deducts.singular')]));

        return redirect(route('hr.deducts.index'));
    }

    /**
     * Remove the specified HrDeduct from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $deduct = $this->hrDeductRepository->find($id);




       
        if (empty($deduct)  || $deduct->HrSalaryDeduct()->count() > 0) {
            flash()->error(__('hr::models/hr_deducts.singular') . ' ' . __('messages.not_found'));
            return redirect(route('hr.deducts.index'));
        }

        $this->hrDeductRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_deducts.singular')]));

        return redirect(route('hr.deducts.index'));
    }
}
