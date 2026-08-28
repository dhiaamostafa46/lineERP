<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateThemeRequest;
use App\Http\Requests\UpdateThemeRequest;
use App\Repositories\ThemeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Response;

class ThemeController extends AppBaseController
{
    /** @var ThemeRepository $themeRepository*/
    private $themeRepository;

    public function __construct(ThemeRepository $themeRepo)
    {
        $this->themeRepository = $themeRepo;
    }

    /**
     * Display a listing of the Theme.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $themes = $this->themeRepository->paginate(10);

        return view('themes.index')
            ->with('themes', $themes);
    }

    /**
     * Show the form for creating a new Theme.
     *
     * @return Response
     */
    public function create()
    {
        return view('themes.create');
    }

    /**
     * Store a newly created Theme in storage.
     *
     * @param CreateThemeRequest $request
     *
     * @return Response
     */
    public function store(CreateThemeRequest $request)
    {
        $input = $request->all();

        $theme = $this->themeRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models.themes.singular')]));

        return redirect(route('themes.index'));
    }

    /**
     * Display the specified Theme.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $theme = $this->themeRepository->find($id);

        if (empty($theme)) {
            Flash::error(__('messages.not_found', ['model' => __('models.themes.singular')]));

            return redirect(route('themes.index'));
        }

        return view('themes.show')->with('theme', $theme);
    }

    /**
     * Show the form for editing the specified Theme.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $theme = $this->themeRepository->find($id);

        if (empty($theme)) {
            Flash::error(__('messages.not_found', ['model' => __('models.themes.singular')]));

            return redirect(route('themes.index'));
        }

        return view('themes.edit')->with('theme', $theme);
    }

    /**
     * Update the specified Theme in storage.
     *
     * @param int $id
     * @param UpdateThemeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateThemeRequest $request)
    {
        $theme = $this->themeRepository->find($id);

        if (empty($theme)) {
            Flash::error(__('messages.not_found', ['model' => __('models.themes.singular')]));

            return redirect(route('themes.index'));
        }

        $theme = $this->themeRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models.themes.singular')]));

        return redirect(route('themes.index'));
    }

    /**
     * Remove the specified Theme from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        $theme = $this->themeRepository->find($id);

        if (empty($theme)) {
            Flash::error(__('messages.not_found', ['model' => __('models.themes.singular')]));

            return redirect(route('themes.index'));
        }

        $this->themeRepository->delete($id);

        Flash::success(__('messages.deleted', ['model' => __('models.themes.singular')]));

        return redirect(route('themes.index'));
    }
}
