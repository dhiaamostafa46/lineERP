<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrDocumentTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrDocumentTypeRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrDocumentTypeRepository;
use Illuminate\Http\Request;


class HrDocumentTypeController extends AppBaseController
{
    /** @var HrDocumentTypeRepository $hrDocumentTypeRepository*/
    private $hrDocumentTypeRepository;

    public function __construct(HrDocumentTypeRepository $hrDocumentTypeRepo)
    {
        $this->hrDocumentTypeRepository = $hrDocumentTypeRepo;
    }

    /**
     * Display a listing of the HrDocumentType.
     */
    public function index(Request $request)
    {
        $data['document_types'] = $this->hrDocumentTypeRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['statuses'] = $this->hrDocumentTypeRepository->statuses();
        return view('hr::document_types.index', $data);
    }

    /**
     * Show the form for creating a new HrDocumentType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrDocumentTypeRepository->statuses();

        return view('hr::document_types.create', $data);
    }

    /**
     * Store a newly created HrDocumentType in storage.
     */
    public function store(CreateHrDocumentTypeRequest $request)
    {
        $input = $request->all();
        $document_type = $this->hrDocumentTypeRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_document_types.singular')]));

        return redirect(route('hr.document_types.index'));
    }

    /**
     * Display the specified HrDocumentType.
     */
    public function show($id)
    {
        $document_type = $this->hrDocumentTypeRepository->find($id);

        if (empty($document_type)) {
            flash()->error(__('hr::models/hr_document_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.document_types.index'));
        }

        return view('hr::document_types.show')->with('hrDocumentType', $document_type);
    }

    /**
     * Show the form for editing the specified HrDocumentType.
     */
    public function edit($id)
    {
        $data['document_type'] = $this->hrDocumentTypeRepository->find($id);

        if (empty($data['document_type'])) {
            flash()->error(__('hr::models/hr_document_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.document_types.index'));
        }
        $data['statuses'] = $this->hrDocumentTypeRepository->statuses();

        return view('hr::document_types.edit', $data);
    }

    /**
     * Update the specified HrDocumentType in storage.
     */
    public function update($id, UpdateHrDocumentTypeRequest $request)
    {
        $document_type = $this->hrDocumentTypeRepository->find($id);

        if (empty($document_type)) {
            flash()->error(__('hr::models/hr_document_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.document_types.index'));
        }

        $document_type = $this->hrDocumentTypeRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_document_types.singular')]));

        return redirect(route('hr.document_types.index'));
    }

    /**
     * Remove the specified HrDocumentType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $document_type = $this->hrDocumentTypeRepository->find($id);

        if (empty($document_type)) {
            flash()->error(__('hr::models/hr_document_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.document_types.index'));
        }

        $this->hrDocumentTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_document_types.singular')]));

        return redirect(route('hr.document_types.index'));
    }
}
