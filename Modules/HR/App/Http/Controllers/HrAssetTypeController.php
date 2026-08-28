<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrAssetTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrAssetTypeRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrAssetTypeRepository;
use Illuminate\Http\Request;


class HrAssetTypeController extends AppBaseController
{
    /** @var HrAssetTypeRepository $hrAssetTypeRepository*/
    private $hrAssetTypeRepository;

    public function __construct(HrAssetTypeRepository $hrAssetTypeRepo)
    {
        $this->hrAssetTypeRepository = $hrAssetTypeRepo;
    }

    /**
     * Display a listing of the HrAssetType.
     */
    public function index(Request $request)
    {
        $data['asset_types'] = $this->hrAssetTypeRepository->paginate(10);
        $data['statuses'] = $this->hrAssetTypeRepository->statuses();

        return view('hr::asset_types.index', $data);
    }

    /**
     * Show the form for creating a new HrAssetType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrAssetTypeRepository->statuses();

        return view('hr::asset_types.create', $data);
    }

    /**
     * Store a newly created HrAssetType in storage.
     */
    public function store(CreateHrAssetTypeRequest $request)
    {
        $input = $request->all();

        $asset_type = $this->hrAssetTypeRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_asset_types.singular')]));

        return redirect(route('hr.asset_types.index'));
    }

    /**
     * Display the specified HrAssetType.
     */
    public function show($id)
    {
        $asset_type = $this->hrAssetTypeRepository->find($id);

        if (empty($asset_type)) {
            flash()->error(__('hr::models/hr_asset_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.asset_types.index'));
        }

        return view('hr::asset_types.show')->with('hrAssetType', $asset_type);
    }

    /**
     * Show the form for editing the specified HrAssetType.
     */
    public function edit($id)
    {
        $data['asset_type'] = $this->hrAssetTypeRepository->find($id);

        if (empty($data['asset_type'])) {
            flash()->error(__('hr::models/hr_asset_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.asset_types.index'));
        }
        $data['statuses'] = $this->hrAssetTypeRepository->statuses();

        return view('hr::asset_types.edit', $data);
    }

    /**
     * Update the specified HrAssetType in storage.
     */
    public function update($id, UpdateHrAssetTypeRequest $request)
    {
        $asset_type = $this->hrAssetTypeRepository->find($id);

        if (empty($asset_type)) {
            flash()->error(__('hr::models/hr_asset_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.asset_types.index'));
        }

        $asset_type = $this->hrAssetTypeRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_asset_types.singular')]));

        return redirect(route('hr.asset_types.index'));
    }

    /**
     * Remove the specified HrAssetType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $asset_type = $this->hrAssetTypeRepository->find($id);

        if (empty($asset_type)) {
            flash()->error(__('hr::models/hr_asset_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.asset_types.index'));
        }

        $this->hrAssetTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_asset_types.singular')]));

        return redirect(route('hr.asset_types.index'));
    }
}
