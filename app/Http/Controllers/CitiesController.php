<?php

namespace App\Http\Controllers;

use App\Http\Requests\citiesRequest;
use App\Models\Area;
use App\Models\City;
use App\Repositories\CityRepository;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function __construct(
        private CityRepository $cityRepository
    ) {}

    public function index(Request $request)
    {
        $data['Cities'] = $this->cityRepository->allQuery($request->all())
            ->with(['translations', 'area.translations'])
            ->latest()
            ->paginate($request->get('pagination', 10));
        $data['statuses'] = $this->cityRepository->statuses();
        $data['areas'] = Area::query()->with('translations')->orderBy('code')->get();

        return view('Cities.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->cityRepository->statuses();
        $data['areas'] = Area::query()->with('translations')->orderBy('code')->get();

        return view('Cities.create', $data);
    }

    public function store(citiesRequest $request)
    {
        $validated = $request->validated();
        $city = new City;
        $city->area_id = (int) $validated['area_id'];
        $city->code = $validated['code'];
        $city->status = (int) $validated['status'];
        $city->save();
        $this->syncCityTranslations($city, $validated);
        $city->save();

        flash()->success(__('messages.saved', ['model' => __('models/Cities.singular')]));

        return redirect(route('Cities.index'));
    }

    public function show($id)
    {
        $City = $this->cityRepository->find((int) $id);

        if (empty($City)) {
            flash()->error(__('models/Cities.singular').' '.__('messages.not_found'));

            return redirect(route('Cities.index'));
        }

        $City->load(['translations', 'area.translations']);

        return view('Cities.show')->with('City', $City);
    }

    public function edit($id)
    {
        $data['City'] = $this->cityRepository->find((int) $id);

        if (empty($data['City'])) {
            flash()->error(__('models/Cities.singular').' '.__('messages.not_found'));

            return redirect(route('Cities.index'));
        }
        $data['statuses'] = $this->cityRepository->statuses();
        $data['areas'] = Area::query()->with('translations')->orderBy('code')->get();

        return view('Cities.edit', $data);
    }

    public function update($id, citiesRequest $request)
    {
        $City = $this->cityRepository->find((int) $id);

        if (empty($City)) {
            flash()->error(__('models/Cities.singular').' '.__('messages.not_found'));

            return redirect(route('Cities.index'));
        }

        $validated = $request->validated();
        $City->area_id = (int) $validated['area_id'];
        $City->code = $validated['code'];
        $City->status = (int) $validated['status'];
        $this->syncCityTranslations($City, $validated);
        $City->save();

        flash()->success(__('messages.updated', ['model' => __('models/Cities.singular')]));

        return redirect(route('Cities.index'));
    }

    public function destroy($id)
    {
        $City = $this->cityRepository->find((int) $id);

        if (empty($City)) {
            flash()->error(__('models/Cities.singular').' '.__('messages.not_found'));

            return redirect(route('Cities.index'));
        }

        $this->cityRepository->delete((int) $id);

        flash()->success(__('messages.deleted', ['model' => __('models/Cities.singular')]));

        return redirect(route('Cities.index'));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncCityTranslations(City $city, array $validated): void
    {
        foreach (config('langs') as $locale => $_language) {
            $city->translateOrNew($locale)->name = $validated[$locale]['name'];
        }
    }
}
