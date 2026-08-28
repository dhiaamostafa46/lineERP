<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrAssetRequest;
use Modules\HR\App\Http\Requests\UpdateHrAssetRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrAssetRepository;
use Illuminate\Http\Request;


class HrAssetController extends AppBaseController
{
    /** @var HrAssetRepository $hrAssetRepository*/
    private $hrAssetRepository;

    public function __construct(HrAssetRepository $hrAssetRepo)
    {
        $this->hrAssetRepository = $hrAssetRepo;
    }

    /**
     * Display a listing of the HrAsset.
     */
    public function index(Request $request)
    {
        $data['assets'] = $this->hrAssetRepository->paginate(10);
        $data['types'] = $this->hrAssetRepository->types();
        $data['departments'] = $this->hrAssetRepository->departments();
        $data['statuses'] = $this->hrAssetRepository->statuses();
        return view('hr::assets.index', $data);
    }

    /**
     * Show the form for creating a new HrAsset.
     */
    public function create()
    {
        $data['types'] = $this->hrAssetRepository->types();
        $data['departments'] = $this->hrAssetRepository->departments();
        $data['statuses'] = $this->hrAssetRepository->statuses();

        return view('hr::assets.create', $data);
    }

    /**
     * Store a newly created HrAsset in storage.
     */
    public function store(CreateHrAssetRequest $request)
    {
        $input = $request->all();

        $asset = $this->hrAssetRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hr_assets.singular')]));

        return redirect(route('hr.assets.index'));
    }

    /**
     * Display the specified HrAsset.
     */
    public function show($id)
    {
        $data['asset'] = $this->hrAssetRepository->find($id);

        if (empty($data['asset'])) {
            flash()->error(__('models/hr_assets.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.assets.index'));
        }

        return view('hr::assets.show', $data);
    }

    /**
     * Show the form for editing the specified HrAsset.
     */
    public function edit($id)
    {
        $data['asset'] = $this->hrAssetRepository->find($id);

        if (empty($data['asset'])) {
            flash()->error(__('models/hr_assets.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.assets.index'));
        }

        $data['types'] = $this->hrAssetRepository->types();
        $data['departments'] = $this->hrAssetRepository->departments();
        $data['statuses'] = $this->hrAssetRepository->statuses();

        return view('hr::assets.edit', $data);
    }

    /**
     * Update the specified HrAsset in storage.
     */
    public function update($id, UpdateHrAssetRequest $request)
    {
        $asset = $this->hrAssetRepository->find($id);

        if (empty($asset)) {
            flash()->error(__('models/hr_assets.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.assets.index'));
        }

        $asset = $this->hrAssetRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/hr_assets.singular')]));

        return redirect(route('hr.assets.index'));
    }

    /**
     * Remove the specified HrAsset from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $asset = $this->hrAssetRepository->find($id);

        if (empty($asset)) {
            flash()->error(__('models/hr_assets.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.assets.index'));
        }

        $this->hrAssetRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hr_assets.singular')]));

        return redirect(route('hr.assets.index'));
    }
}
