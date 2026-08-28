<?php

namespace App\Http\Controllers;

use App\Http\Requests\companiesRequest;
use App\Models\City;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function __construct(
        private CompanyRepository $companyRepository
    ) {}

    public function index(Request $request)
    {
        $data['Companies'] = $this->companyRepository->allQuery($request->all())
            ->with(['translations', 'city.translations'])
            ->latest()
            ->paginate($request->get('pagination', 10));
        $data['statuses'] = $this->companyRepository->statuses();
        $data['cities'] = City::query()->with('translations')->orderBy('code')->get();

        return view('Companies.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->companyRepository->statuses();
        $data['cities'] = City::query()->with('translations')->orderBy('code')->get();

        return view('Companies.create', $data);
    }

    public function store(companiesRequest $request)
    {
        $validated = $request->validated();
        $company = new Company;
        $company->code = $validated['code'];
        $company->phone = $validated['phone'] ?? null;
        $company->email = $validated['email'] ?? null;
        $company->contact_person = $validated['contact_person'] ?? null;
        $company->address = $validated['address'] ?? null;
        $company->city_id = isset($validated['city_id']) ? (int) $validated['city_id'] : null;
        $company->status = (int) $validated['status'];
        if (auth()->check()) {
            $company->created_by = auth()->id();
        }
        $company->save();
        $this->syncCompanyTranslations($company, $validated);
        if (auth()->check()) {
            $company->updated_by = auth()->id();
        }
        $company->save();

        flash()->success(__('messages.saved', ['model' => __('models/Companies.singular')]));

        return redirect(route('Companies.index'));
    }

    public function show($id)
    {
        $Company = $this->companyRepository->find((int) $id);

        if (empty($Company)) {
            flash()->error(__('models/Companies.singular').' '.__('messages.not_found'));

            return redirect(route('Companies.index'));
        }

        $Company->load(['translations', 'city.translations']);

        return view('Companies.show')->with('Company', $Company);
    }

    public function edit($id)
    {
        $data['Company'] = $this->companyRepository->find((int) $id);

        if (empty($data['Company'])) {
            flash()->error(__('models/Companies.singular').' '.__('messages.not_found'));

            return redirect(route('Companies.index'));
        }
        $data['statuses'] = $this->companyRepository->statuses();
        $data['cities'] = City::query()->with('translations')->orderBy('code')->get();

        return view('Companies.edit', $data);
    }

    public function update($id, companiesRequest $request)
    {
        $Company = $this->companyRepository->find((int) $id);

        if (empty($Company)) {
            flash()->error(__('models/Companies.singular').' '.__('messages.not_found'));

            return redirect(route('Companies.index'));
        }

        $validated = $request->validated();
        $Company->code = $validated['code'];
        $Company->phone = $validated['phone'] ?? null;
        $Company->email = $validated['email'] ?? null;
        $Company->contact_person = $validated['contact_person'] ?? null;
        $Company->address = $validated['address'] ?? null;
        $Company->city_id = isset($validated['city_id']) ? (int) $validated['city_id'] : null;
        $Company->status = (int) $validated['status'];
        $this->syncCompanyTranslations($Company, $validated);
        if (auth()->check()) {
            $Company->updated_by = auth()->id();
        }
        $Company->save();

        flash()->success(__('messages.updated', ['model' => __('models/Companies.singular')]));

        return redirect(route('Companies.index'));
    }

    public function destroy($id)
    {
        $Company = $this->companyRepository->find((int) $id);

        if (empty($Company)) {
            flash()->error(__('models/Companies.singular').' '.__('messages.not_found'));

            return redirect(route('Companies.index'));
        }

        $this->companyRepository->delete((int) $id);

        flash()->success(__('messages.deleted', ['model' => __('models/Companies.singular')]));

        return redirect(route('Companies.index'));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncCompanyTranslations(Company $company, array $validated): void
    {
        foreach (config('langs') as $locale => $_language) {
            $company->translateOrNew($locale)->name = $validated[$locale]['name'];
        }
    }
}
