<?php

namespace App\Http\Controllers;

use App\Http\Requests\areasRequest;
use App\Models\Area;
use App\Repositories\AreaRepository;
use Illuminate\Http\Request;

class AreasController extends Controller
{
    public function __construct(
        private AreaRepository $areaRepository
    ) {}

    public function index(Request $request)
    {
        $data['Areas'] = $this->areaRepository->allQuery($request->all())
            ->with('translations')
            ->latest()
            ->paginate($request->get('pagination', 10));
        $data['statuses'] = $this->areaRepository->statuses();

        return view('Areas.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->areaRepository->statuses();

        return view('Areas.create', $data);
    }

    public function store(areasRequest $request)
    {
        $validated = $request->validated();
        $area = new Area;
        $area->code = $validated['code'];
        $area->status = (int) $validated['status'];
        $area->save();
        $this->syncAreaTranslations($area, $validated);
        $area->save();

        flash()->success(__('messages.saved', ['model' => __('models/Areas.singular')]));

        return redirect(route('Areas.index'));
    }

    public function show($id)
    {
        $Area = $this->areaRepository->find((int) $id);

        if (empty($Area)) {
            flash()->error(__('models/Areas.singular').' '.__('messages.not_found'));

            return redirect(route('Areas.index'));
        }

        $Area->load(['translations', 'cities']);

        return view('Areas.show')->with('Area', $Area);
    }

    public function edit($id)
    {
        $data['Area'] = $this->areaRepository->find((int) $id);

        if (empty($data['Area'])) {
            flash()->error(__('models/Areas.singular').' '.__('messages.not_found'));

            return redirect(route('Areas.index'));
        }
        $data['statuses'] = $this->areaRepository->statuses();

        return view('Areas.edit', $data);
    }

    public function update($id, areasRequest $request)
    {
        $Area = $this->areaRepository->find((int) $id);

        if (empty($Area)) {
            flash()->error(__('models/Areas.singular').' '.__('messages.not_found'));

            return redirect(route('Areas.index'));
        }

        $validated = $request->validated();
        $Area->code = $validated['code'];
        $Area->status = (int) $validated['status'];
        $this->syncAreaTranslations($Area, $validated);
        $Area->save();

        flash()->success(__('messages.updated', ['model' => __('models/Areas.singular')]));

        return redirect(route('Areas.index'));
    }

    public function destroy($id)
    {
        $Area = $this->areaRepository->find((int) $id);

        if (empty($Area)) {
            flash()->error(__('models/Areas.singular').' '.__('messages.not_found'));

            return redirect(route('Areas.index'));
        }

        if ($Area->cities()->exists()) {
            flash()->error(__('models/Areas.messages.cannot_delete_has_cities'));

            return redirect(route('Areas.index'));
        }

        $this->areaRepository->delete((int) $id);

        flash()->success(__('messages.deleted', ['model' => __('models/Areas.singular')]));

        return redirect(route('Areas.index'));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncAreaTranslations(Area $area, array $validated): void
    {
        foreach (config('langs') as $locale => $_language) {
            $area->translateOrNew($locale)->name = $validated[$locale]['name'];
        }
    }
}
