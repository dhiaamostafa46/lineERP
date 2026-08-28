<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrSettingRequest;
use Modules\HR\App\Http\Requests\UpdateHrSettingRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrSettingRepository;
use Illuminate\Http\Request;



class HrSettingController extends AppBaseController
{
    /** @var HrSettingRepository $hrSettingRepository*/
    private $hrSettingRepository;

    public function __construct(HrSettingRepository $hrSettingRepo)
    {
        $this->hrSettingRepository = $hrSettingRepo;
    }

    /**
     * Display a listing of the HrSetting.
     */
    public function index(Request $request)
    {
        $data['settings'] = $this->hrSettingRepository->paginate(10);


        return view('hr::settings.index', $data);
    }

    /**
     * Show the form for creating a new HrSetting.
     */
    public function create()
    {
        $data['missingFingerprintPolicies'] = $this->hrSettingRepository->missingFingerprintPolicies();


        $data['users'] = $this->hrSettingRepository->users();
        return view('hr::settings.create', $data);
    }

    /**
     * Store a newly created HrSetting in storage.
     */
    public function store(CreateHrSettingRequest $request)
    {
        $input = $request->all();

        $setting = $this->hrSettingRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/hrSettings.singular')]));

        return redirect(route('hr.settings.index'));
    }

    /**
     * Display the specified HrSetting.
     */
    public function show($id)
    {
        $data['setting'] = $this->hrSettingRepository->find($id);

        if (empty($data['setting'])) {
            flash()->error(__('models/hrSettings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.settings.index'));
        }

        return view('hr::settings.show', $data);
    }

    /**
     * Show the form for editing the specified HrSetting.
     */
    public function edit($id)
    {
        $data['setting'] = $this->hrSettingRepository->find($id);

          $data['missingFingerprintPolicies'] = $this->hrSettingRepository->missingFingerprintPolicies();


        if (empty($data['setting'])) {
            flash()->error(__('models/hrSettings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.settings.edit'));
        }

        $data['users'] = $this->hrSettingRepository->users();
        return view('hr::settings.edit', $data);
    }

    /**
     * Update the specified HrSetting in storage.
     */
    public function update($id, UpdateHrSettingRequest $request)
    {

        $setting = $this->hrSettingRepository->find($id);


        if (empty($setting)) {
            flash()->error(__('models/hrSettings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.settings.index'));
        }
        $setting = $this->hrSettingRepository->update($request->except(['tap', 'tab']), $id);
        flash()->success(__('messages.updated', ['model' => __('models/hrSettings.singular')]));
        return back();
        return redirect(route('hr.settings.index'));
    }

    /**
     * Remove the specified HrSetting from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $setting = $this->hrSettingRepository->find($id);

        if (empty($setting)) {
            flash()->error(__('models/hrSettings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.settings.index'));
        }

        $this->hrSettingRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hrSettings.singular')]));

        return redirect(route('hr.settings.index'));
    }
}
