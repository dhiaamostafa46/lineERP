<?php

namespace Modules\Invoices\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Invoices\App\Repositories\InvSettingRepository;
use Modules\Invoices\App\Repositories\ZatcaSettingRepository;
use Modules\Invoices\App\Http\Requests\InvSettingRequest;
use Modules\Invoices\App\Http\Requests\InvZatcaSettingRequest;
use Modules\Invoices\App\Services\ZatcaService;
use App\Models\Organization;
use App\Models\Branch;

class InvSettingController extends Controller
{
    private InvSettingRepository  $settingRepository;
    private ZatcaSettingRepository $zatcaRepository;

    public function __construct(
        InvSettingRepository   $settingRepo,
        ZatcaSettingRepository $zatcaRepo
    ) {
        $this->settingRepository = $settingRepo;
        $this->zatcaRepository   = $zatcaRepo;
    }

    /** Display a listing of the resource. */
    public function index()
    {
        return view('invoices::setting.index');
    }

    /** Show the form for creating a new resource. */
    public function create()
    {
        return view('invoices::create');
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request)
    {
        //
    }

    /** Show the specified resource. */
    public function show($id)
    {
        return view('invoices::show');
    }

    /**
     * Show the General Invoice Settings form.
     */
    public function edit($id)
    {
        $settinga = $this->settingRepository->firstOrNew();

        return view('invoices::setting.edit', compact('settinga'));
    }

    /**
     * Update the General Invoice Settings (Sales / Purchase / VAT / etc.)
     * ZATCA fields are intentionally excluded here.
     */
    public function update(InvSettingRequest $request, $id)
    {
        $validated = $request->validated();

        // Filter out zatca_ keys — those are handled by zatcaStore()
        $setting_data = array_filter(
            $validated,
            fn($key) => !str_starts_with($key, 'zatca_'),
            ARRAY_FILTER_USE_KEY
        );

        try {
            $setting = $this->settingRepository->updateSettings($setting_data);

            flash()->success(__('messages.updated', [
                'model' => __('invoices::models/invoices_setting.singular'),
            ]));

            return redirect()->route('invoices.Setting.edit', $setting->id);

        } catch (\Throwable $e) {
            flash()->error(__('messages.error_updating', [
                'model' => __('invoices::models/invoices_setting.singular'),
            ]) . ': ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the dedicated ZATCA Phase 2 settings page.
     */
    public function zatca(Request $request)
    {
        $organization = Organization::first();
        $branches = Branch::activeOnly()->get();
        
        $tax_type = $request->query('tax_type');
        $branch_id = $request->query('branch_id');

        if ($organization) {
            if ($tax_type === 'unified') {
                if ($organization->tax_registration_type !== 'unified') {
                    $organization->update(['tax_registration_type' => 'unified']);
                }
                $branch_id = null;
            } elseif ($tax_type === 'branches' || !empty($branch_id)) {
                if ($organization->tax_registration_type !== 'branches') {
                    $organization->update(['tax_registration_type' => 'branches']);
                }
                if (empty($branch_id)) {
                    $branch_id = $branches->first()->id ?? null;
                }
            } else {
                // Preserve current database registration type on direct visit
                if ($organization->tax_registration_type === 'branches') {
                    $branch_id = $branches->first()->id ?? null;
                } else {
                    $branch_id = null;
                }
            }
        }

        $zatca_setting = $this->zatcaRepository->firstOrNew($branch_id);

        // Auto-fill defaults if they are not already set
        if (empty($zatca_setting->common_name)) {
            if ($branch_id) {
                $selected_branch = $branches->where('id', $branch_id)->first();
                $zatca_setting->common_name = $selected_branch->name ?? '';
            } else {
                $zatca_setting->common_name = $organization->name ?? '';
            }
        }

        if (empty($zatca_setting->organization_name)) {
            // Default to Organization Arabic Name as requested
            $zatca_setting->organization_name = $organization->translate('ar')->name ?? $organization->name ?? '';
        }

        return view('invoices::setting.zatca', compact('zatca_setting', 'organization', 'branches', 'branch_id'));
    }

    /**
     * Save the ZATCA Phase 2 settings (separate from general settings).
     * And automatically Onboard if OTP is provided.
     */
    public function zatcaStore(InvZatcaSettingRequest $request, ZatcaService $zatcaService, $id)
    {
        $validated = $request->validated();
        $branch_id = $request->input('branch_id');
        $branch_id = empty($branch_id) ? null : $branch_id;

        try {
            // 1. Save Settings (Exclude environment to prevent premature switching)
            $saveData = array_filter(
                $validated,
                fn($key) => $key !== 'zatca_environment',
                ARRAY_FILTER_USE_KEY
            );
            
            $setting = $this->zatcaRepository->updateSettings($saveData, $branch_id);

            // 2. Onboard if OTP is present
            if (!empty($request->zatca_otp)) {
                $zatcaService->onboard($setting, $request->zatca_otp, $request->zatca_environment);
                flash()->success(__('invoices::models/invoices_setting.fields.zatca_success_linked'));
            } else {
                flash()->success(__('messages.updated', [
                    'model' => __('invoices::models/invoices_setting.sections.taxes_and_zakat'),
                ]));
            }

            return redirect()->route('invoices.Setting.zatca', ['branch_id' => $branch_id]);

        } catch (\Throwable $e) {
            flash()->error(__('messages.error_updating', [
                'model' => __('invoices::models/invoices_setting.sections.taxes_and_zakat'),
            ]) . ': ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /** Remove the specified resource from storage. */
    public function destroy($id)
    {
        //
    }

    /**
     * Request Production CSID (Final Link Step).
     **/
    public function requestProduction(Request $request, ZatcaService $zatcaService, $id)
    {
        try {
            $branch_id = $request->input('branch_id');
            $setting = $this->zatcaRepository->firstOrNew($branch_id);
            
            $zatcaService->getProductionCSID($setting);

            flash()->success(__('invoices::models/invoices_setting.fields.zatca_success_production'));
            
            return redirect()->route('invoices.Setting.zatca', ['branch_id' => $branch_id]);

        } catch (\Throwable $e) {
            flash()->error(__('messages.error_updating', [
                'model' => __('invoices::models/invoices_setting.sections.taxes_and_zakat'),
            ]) . ': ' . $e->getMessage());

            return redirect()->back();
        }
    }
}
