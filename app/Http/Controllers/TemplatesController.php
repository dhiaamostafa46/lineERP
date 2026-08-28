<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Repositories\TemplateRepository;
use Illuminate\Http\Request;

class TemplatesController extends AppBaseController
{
    private $TemplateRepository;

    public function __construct(TemplateRepository $TemplateRepository)
    {
        $this->TemplateRepository = $TemplateRepository;
    }

    /**
     * Display a listing of the Templates.
     */
    public function index(Request $request)
    {
        $data['Templates'] = $this->TemplateRepository->allQuery($request->all())->latest()->paginate($request->pagination ?? 10);
        $data['statuses'] = $this->TemplateRepository->statuses();

        return view('Templates.index', $data);
    }

    /**
     * Show the form for creating a new Template.
     */
    public function create()
    {
        $data['statuses'] = $this->TemplateRepository->statuses();

        return view('Templates.create', $data);
    }

    /**
     * 
     * Store a newly created Template in storage.
     */
    public function store(TemplateRequest $request)
    {
        $input = $request->all();

        $Template = $this->TemplateRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/Templates.singular')]));

        return redirect(route('Templates.index'));
    }

    /**
     * Display the specified Template.
     */
    public function show($id)
    {

        
        $Template = $this->TemplateRepository->find($id);


        if (empty($Template)) {
            flash()->error(__('models/Templates.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Templates.index'));
        }

        return view('Templates.show')->with('Template', $Template);
    }

    /**
     * Show the form for editing the specified Template.
     */
    public function edit($id)
    {
        $Template = $this->TemplateRepository->find($id);

        if (empty($Template)) {
            flash()->error(__('models/Templates.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Templates.index'));
        }

        $data['Template'] = $Template;
        $data['statuses'] = $this->TemplateRepository->statuses();

        return view('Templates.edit', $data);
    }

    /**
     * Update the specified Template in storage.
     */
    public function update($id, TemplateRequest $request)
    {
        $Template = $this->TemplateRepository->find($id);

        if (empty($Template)) {
            flash()->error(__('models/Templates.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Templates.index'));
        }

        $Template = $this->TemplateRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/Templates.singular')]));

        return redirect(route('Templates.index'));
    }

    /**
     * Remove the specified Template from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $Template = $this->TemplateRepository->find($id);

        if (empty($Template)) {
            flash()->error(__('models/Templates.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Templates.index'));
        }

        $this->TemplateRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/Templates.singular')]));

        return redirect(route('Templates.index'));
    }

    /**
     * Show the visual builder for the specified Template.
     */
    public function builder($id)
    {
        $Template = $this->TemplateRepository->find($id);

        if (empty($Template)) {
            flash()->error(__('models/Templates.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Templates.index'));
        }

        return view('Templates.editor')->with('Template', $Template);
    }
}
