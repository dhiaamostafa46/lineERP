<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrDocumentRequest;
use Modules\HR\App\Http\Requests\UpdateHrDocumentRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Employee;
use Modules\HR\App\Models\HrDocumentType;
use Modules\HR\App\Repositories\HrDocumentRepository;
use Illuminate\Http\Request;

use Modules\HR\App\Models\HrDocument;

class HrDocumentController extends AppBaseController
{
    /** @var HrDocumentRepository $hrDocumentRepository*/
    private $hrDocumentRepository;

    public function __construct(HrDocumentRepository $hrDocumentRepo)
    {
        $this->hrDocumentRepository = $hrDocumentRepo;
    }

    /**
     * Display a listing of the HrDocument.
     */
    public function index(Request $request)
    {
        $data['documents'] = $this->hrDocumentRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);
        $data['types'] = $this->hrDocumentRepository->types();
        $data['statuses'] = $this->hrDocumentRepository->statuses();
        $data['employees'] = $this->hrDocumentRepository->employees();

      

        return view('hr::documents.index', $data);
    }

    /**
     * Show the form for creating a new HrDocument.
     */
    public function create()
    {
        $data['employees'] = $this->hrDocumentRepository->employees();
        $data['types'] = $this->hrDocumentRepository->types();
        $data['statuses'] = $this->hrDocumentRepository->statuses();

        return view('hr::documents.create', $data);
    }

    /**
     * Store a newly created HrDocument in storage.
     */
    public function store(CreateHrDocumentRequest $request)
    {
        $input = $request->all();

        $hrDocument = $this->hrDocumentRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_documents.singular')]));

        return redirect(route('hr.documents.index'));
    }

    /**
     * Display the specified HrDocument.
     */
    public function show($id)
    {
        $data['document'] = $this->hrDocumentRepository->find($id);

        if (empty($data['document'])) {
            flash()->error(__('hr::models/hr_documents.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr_documents.index'));
        }

        return view('hr::documents.show', $data);
    }

    /**
     * Show the form for editing the specified HrDocument.
     */
    public function edit($id)
    {
        $data['document'] = $this->hrDocumentRepository->find($id);

        if (empty($data['document'])) {
            flash()->error(__('hr::models/hr_documents.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr_documents.index'));
        }
        $data['employees'] = $this->hrDocumentRepository->employees();
        $data['types'] = $this->hrDocumentRepository->types();
        $data['statuses'] = $this->hrDocumentRepository->statuses();
        return view('hr::documents.edit', $data);
    }

    /**
     * Update the specified HrDocument in storage.
     */
    public function update($id, UpdateHrDocumentRequest $request)
    {
        $hrDocument = $this->hrDocumentRepository->find($id);

        if (empty($hrDocument)) {
            flash()->error(__('hr::models/hr_documents.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr_documents.index'));
        }

        $hrDocument = $this->hrDocumentRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_documents.singular')]));

        return redirect(route('hr.documents.index'));
    }

    /**
     * Remove the specified HrDocument from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $hrDocument = $this->hrDocumentRepository->find($id);

        if (empty($hrDocument)) {
            flash()->error(__('hr::models/hr_documents.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.documents.index'));
        }

        $this->hrDocumentRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_documents.singular')]));

        return redirect(route('hr.documents.index'));
    }
}
