<?php

namespace Modules\AccuSoft\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\AccuSoft\App\Models\AccountingSettings;

class AsAccountingSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['settings'] = AccountingSettings::getInstance();
        $data['depreciationMethods'] = AccountingSettings::depreciationMethods();
        $data['depreciationFrequencies'] = AccountingSettings::depreciationFrequencies();
        return view('accusoft::accounting_settings.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $settings = AccountingSettings::getInstance();
            $validator = Validator::make($request->all(), [
                'currency' => 'string|max:10',
                'decimal_places' => 'integer|min:0|max:4',
                'journal_prefix' => 'string|max:10',
                'journal_next_number' => 'integer|min:1',
                'allow_backdated_entries' => 'boolean',
                'allow_future_dated_entries' => 'boolean',
                'default_depreciation_method' => 'integer',
                'depreciation_frequency' => 'integer',
                'auto_post_depreciation_entries' => 'boolean',
                'hr_auto_post_journal_entries' => 'boolean',
                'vehicle_auto_post_journal_entries' => 'boolean',
                'driver_auto_post_journal_entries' => 'boolean',

            ]);

             

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $settings->update($request->all());

            flash()->success(__('messages.updated', ['model' => __('accusoft::models/as_accounting_settings.singular')]));

            return redirect()->route('accusoft.AccountingSettings.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('accusoft::models/as_accounting_settings.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
