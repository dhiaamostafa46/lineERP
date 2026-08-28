<?php

namespace App\Http\Controllers;

use App\Http\Requests\companyContractsRequest;
use App\Models\Company;
use App\Models\CompanyContract;
use App\Repositories\CompanyContractRepository;
use Illuminate\Http\Request;

class CompanyContractsController extends Controller
{
    public function __construct(
        private CompanyContractRepository $companyContractRepository
    ) {}

    public function index(Request $request)
    {
        $data['CompanyContracts'] = $this->companyContractRepository->allQuery($request->all())
            ->with(['company.translations'])
            ->latest()
            ->paginate($request->get('pagination', 10));
        $data['statuses'] = $this->companyContractRepository->statuses();
        $data['companies'] = Company::query()->with('translations')->orderBy('code')->get();

        return view('CompanyContracts.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->companyContractRepository->statuses();
        $data['companies'] = Company::query()->with('translations')->orderBy('code')->get();
        $data['companyPricingTypes'] = $this->companyContractRepository->companyPricingTypes();
        $data['driverPaymentTypes'] = $this->companyContractRepository->driverPaymentTypes();
        $data['settlementCycles'] = $this->companyContractRepository->settlementCycles();

        return view('CompanyContracts.create', $data);
    }

    public function store(companyContractsRequest $request)
    {
        $validated = $request->validated();
        $contract = new CompanyContract;
        $contract->company_id = (int) $validated['company_id'];
        $contract->company_pricing_type = $validated['company_pricing_type'];
        $contract->company_pricing_value = $validated['company_pricing_value'];
        $contract->driver_payment_type = $validated['driver_payment_type'];
        $contract->driver_payment_value = $validated['driver_payment_value'];
        $contract->settlement_cycle = $validated['settlement_cycle'];
        $contract->start_date = $validated['start_date'] ?? null;
        $contract->end_date = $validated['end_date'] ?? null;
        $contract->status = (int) $validated['status'];
        $contract->notes = $validated['notes'] ?? null;
        if (auth()->check()) {
            $contract->created_by = auth()->id();
        }
        $contract->save();

        flash()->success(__('messages.saved', ['model' => __('models/CompanyContracts.singular')]));

        return redirect(route('CompanyContracts.index'));
    }

    public function show($id)
    {
        $CompanyContract = $this->companyContractRepository->find((int) $id);

        if (empty($CompanyContract)) {
            flash()->error(__('models/CompanyContracts.singular').' '.__('messages.not_found'));

            return redirect(route('CompanyContracts.index'));
        }

        $CompanyContract->load(['company.translations']);

        return view('CompanyContracts.show')->with('CompanyContract', $CompanyContract);
    }

    public function edit($id)
    {
        $data['CompanyContract'] = $this->companyContractRepository->find((int) $id);

        if (empty($data['CompanyContract'])) {
            flash()->error(__('models/CompanyContracts.singular').' '.__('messages.not_found'));

            return redirect(route('CompanyContracts.index'));
        }
        $data['statuses'] = $this->companyContractRepository->statuses();
        $data['companies'] = Company::query()->with('translations')->orderBy('code')->get();
        $data['companyPricingTypes'] = $this->companyContractRepository->companyPricingTypes();
        $data['driverPaymentTypes'] = $this->companyContractRepository->driverPaymentTypes();
        $data['settlementCycles'] = $this->companyContractRepository->settlementCycles();

        return view('CompanyContracts.edit', $data);
    }

    public function update($id, companyContractsRequest $request)
    {
        $CompanyContract = $this->companyContractRepository->find((int) $id);

        if (empty($CompanyContract)) {
            flash()->error(__('models/CompanyContracts.singular').' '.__('messages.not_found'));

            return redirect(route('CompanyContracts.index'));
        }

        $validated = $request->validated();
        $CompanyContract->company_id = (int) $validated['company_id'];
        $CompanyContract->company_pricing_type = $validated['company_pricing_type'];
        $CompanyContract->company_pricing_value = $validated['company_pricing_value'];
        $CompanyContract->driver_payment_type = $validated['driver_payment_type'];
        $CompanyContract->driver_payment_value = $validated['driver_payment_value'];
        $CompanyContract->settlement_cycle = $validated['settlement_cycle'];
        $CompanyContract->start_date = $validated['start_date'] ?? null;
        $CompanyContract->end_date = $validated['end_date'] ?? null;
        $CompanyContract->status = (int) $validated['status'];
        $CompanyContract->notes = $validated['notes'] ?? null;
        if (auth()->check()) {
            $CompanyContract->updated_by = auth()->id();
        }
        $CompanyContract->save();

        flash()->success(__('messages.updated', ['model' => __('models/CompanyContracts.singular')]));

        return redirect(route('CompanyContracts.index'));
    }

    public function destroy($id)
    {
        $CompanyContract = $this->companyContractRepository->find((int) $id);

        if (empty($CompanyContract)) {
            flash()->error(__('models/CompanyContracts.singular').' '.__('messages.not_found'));

            return redirect(route('CompanyContracts.index'));
        }

        $this->companyContractRepository->delete((int) $id);

        flash()->success(__('messages.deleted', ['model' => __('models/CompanyContracts.singular')]));

        return redirect(route('CompanyContracts.index'));
    }
}
