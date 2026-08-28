<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SettingRepository;
use Illuminate\Http\Request;
use Flash;

class SettingController extends AppBaseController
{
    /** @var SettingRepository $settingRepository*/
    private $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    /**
     * Show the form for editing the specified Setting.
     */
    public function edit($id)
    {
        $setting = $this->settingRepository->find($id);

        if (empty($setting)) {
            flash()->error(__('models/settings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('settings.index'));
        }

        return view('settings.edit')->with('setting', $setting);
    }

    /**
     * Update the specified Setting in storage.
     */
    public function update($id, UpdateSettingRequest $request)
    {
        $setting = $this->settingRepository->find($id);
        if ($request->coming_soon) {
            $setting->update(['coming_soon' => 1]);
        } else {
            $setting->update(['coming_soon' => 0]);
        }

        if (empty($setting)) {
            flash()->error(__('models/settings.singular') . ' ' . __('messages.not_found'));

            return redirect(route('settings.index'));
        }

        $setting = $this->settingRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/settings.singular')]));

        return back();
    }
}
