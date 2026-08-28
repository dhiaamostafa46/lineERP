<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrCustodyRequest;
use Modules\HR\App\Http\Requests\UpdateHrCustodyRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrCustodyRepository;
use Illuminate\Http\Request;

class HrCustodyController extends AppBaseController
{
    /** @var HrCustodyRepository $hrCustodyRepository*/
    private $hrCustodyRepository;

    public function __construct(HrCustodyRepository $hrCustodyRepo)
    {
        $this->hrCustodyRepository = $hrCustodyRepo;
    }

    /**
     * Display a listing of the HrCustody.
     */
    public function index(Request $request)
    {
        $data['custodies'] = $this->hrCustodyRepository->allQuery($request->except('pagination'))->paginate(10);
        $data['employees'] = $this->hrCustodyRepository->employees();
        $data['assets'] = $this->hrCustodyRepository->assets();
        $data['statuses'] = $this->hrCustodyRepository->statuses();
        return view('hr::custodies.index', $data);
    }

    /**
     * Show the form for creating a new HrCustody.
     */
    public function create()
    {
        $data['employees'] = $this->hrCustodyRepository->employees();
        $data['assets'] = $this->hrCustodyRepository->assets();
        $data['statuses'] = $this->hrCustodyRepository->statuses();

        return view('hr::custodies.create', $data);
    }

    /**
     * Store a newly created HrCustody in storage.
     */
    public function store(CreateHrCustodyRequest $request)
    {
        $input = $request->all();

        $custody = $this->hrCustodyRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hr_custodies.singular')]));

        return redirect(route('hr.custodies.index'));
    }

    /**
     * Display the specified HrCustody.
     */
    public function show($id)
    {
        $data['custody'] = $this->hrCustodyRepository->find($id);

        if (empty($data['custody'])) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.custodies.index'));
        }

        return view('hr::custodies.show', $data);
    }

    /**
     * Show the form for editing the specified HrCustody.
     */
    public function edit($id)
    {
        $data['custody'] = $this->hrCustodyRepository->find($id);
        $data['employees'] = $this->hrCustodyRepository->employees();
        $data['assets'] = $this->hrCustodyRepository->assets();
        $data['statuses'] = $this->hrCustodyRepository->statuses();
        $data['hrCustody'] = $data['custody'];

        if (empty($data['custody'])) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.custodies.index'));
        }

        return view('hr::custodies.edit', $data);
    }

    /**
     * Update the specified HrCustody in storage.
     */
    public function update($id, UpdateHrCustodyRequest $request)
    {
        $custody = $this->hrCustodyRepository->find($id);

        if (empty($custody)) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.custodies.index'));
        }

        $custody = $this->hrCustodyRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hr_custodies.singular')]));

        return redirect(route('hr.custodies.index'));
    }

    /**
     * Remove the specified HrCustody from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $custody = $this->hrCustodyRepository->find($id);

        if (empty($custody)) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.custodies.index'));
        }

        $this->hrCustodyRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hr_custodies.singular')]));

        return redirect(route('hr.custodies.index'));
    }

    // received
    public function receive($id)
    {
        $custody = $this->hrCustodyRepository->find($id);

        if (empty($custody)) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return  redirect()->back();
        }

        $this->hrCustodyRepository->received($id);

        flash()->success(__('messages.updated', ['model' => __('models/hr_custodies.singular')]));

        return redirect()->back();
    }


    public function Return($id)
    {

        $custody = $this->hrCustodyRepository->find($id);

        if (empty($custody)) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return  redirect()->back();
        }

        $this->hrCustodyRepository->Return($id);

        flash()->success(__('messages.updated', ['model' => __('models/hr_custodies.singular')]));

        return redirect()->back();
    }


    public function AcceptReturn($id)
    {
        $custody = $this->hrCustodyRepository->find($id);

        if (empty($custody)) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return  redirect()->back();
        }

        $this->hrCustodyRepository->AcceptReturn($id);

        flash()->success(__('messages.updated', ['model' => __('models/hr_custodies.singular')]));

        return redirect()->back();

    }

    public function nonAccept($id)
    {
        $custody = $this->hrCustodyRepository->find($id);

        if (empty($custody)) {
            flash()->error(__('models/hr_custodies.singular') . ' ' . __('messages.not_found'));

            return  redirect()->back();
        }

        $this->hrCustodyRepository->nonAccept($id);

        flash()->success(__('messages.updated', ['model' => __('models/hr_custodies.singular')]));

        return redirect()->back();

    }



}
