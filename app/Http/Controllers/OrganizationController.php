<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    private $OrganizationRepository;
    public function __construct(OrganizationRepository $OrganizationRepository)
    {
        $this->OrganizationRepository = $OrganizationRepository;
    }

    /**
     * Display a listing of the HrAssetType.
     */
    public function index(Request $request)
    {
        $data['Organization'] = $this->OrganizationRepository->paginate(10);
        $data['statuses'] = $this->OrganizationRepository->statuses();
        return view('Organization.index', $data);
    }

    /**
     * Show the form for creating a new HrAssetType.
     */
    public function create()
    {
        $data['statuses'] = $this->OrganizationRepository->statuses();
        return view('Organization.create', $data);
    }

    /**
     * Store a newly created HrAssetType in storage.
     */
    public function store(OrganizationRequest $request)
    {
        $input = $request->all();
        $Organization = $this->OrganizationRepository->create($input);
        flash()->success(__('messages.saved', ['model' => __('models/Organization.singular')]));
        return redirect()->back();
    }

    /**
     * Display the specified HrAssetType.
     */
    public function show($id)
    {
        $Organization = $this->OrganizationRepository->find($id);

        if (empty($Organization)) {
            flash()->error(__('models/Organization.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Organization.index'));
        }

        return view('Organization.show')->with('Organization', $Organization);
    }

    /**
     * Show the form for editing the specified HrAssetType.
     */
    public function edit($id)
    {
        $data['Organization'] = $this->OrganizationRepository->find($id);

        if (empty($data['Organization'])) {
            $data['Organization'] = $this->OrganizationRepository->create([
                'ar' => ['name' => 'المنشاة'],
                'en' => ['name' => 'Organization'],
                'status' => 1,
            ]);
        }
       
        $data['statuses'] = $this->OrganizationRepository->statuses();

        return view('Organization.edit', $data);
    }

    /**
     * Update the specified HrAssetType in storage.
     */
    public function update($id, OrganizationRequest $request)
    {
        $Organization = $this->OrganizationRepository->find($id);

        if (empty($Organization)) {
            flash()->error(__('models/Organization.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Organization.index'));
        }

        $Organization = $this->OrganizationRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/Organization.singular')]));

        return redirect()->back();
    }

    /**
     * Remove the specified HrAssetType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $Organization = $this->OrganizationRepository->find($id);

        if (empty($Organization)) {
            flash()->error(__('models/Organization.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Organization.index'));
        }

        $this->OrganizationRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/Organization.singular')]));

        return redirect(route('Organization.index'));
    }
}
