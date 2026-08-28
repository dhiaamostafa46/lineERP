<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\HR\App\Http\Requests\CreateHrPostRequest;
use Modules\HR\App\Http\Requests\UpdateHrPostRequest;
use Modules\HR\App\Models\HrPost;
use Modules\HR\App\Repositories\HrPostRepository;

class HrPostController extends AppBaseController
{
    public function __construct(private HrPostRepository $hrPostRepository) {}

    public function index(Request $request)
    {
        $data['posts'] = $this->hrPostRepository
            ->allQuery($request->except('pagination'))
            ->with(['translations', 'creator'])
            ->orderByDesc('created_at')
            ->paginate($request->pagination ?? 10);
        $data['types'] = $this->hrPostRepository->types();
        $data['statuses'] = $this->hrPostRepository->statuses();
        $data['flages'] = $this->hrPostRepository->flages();

        return view('hr::posts.index', $data);
    }

    public function create()
    {
        $data['types'] = $this->hrPostRepository->types();
        $data['statuses'] = $this->hrPostRepository->statuses();
        $data['flages'] = $this->hrPostRepository->flages();
        $data['employees'] = $this->hrPostRepository->employees();
        $data['departments'] = $this->hrPostRepository->departments();
        $data['branches'] = $this->hrPostRepository->branches();

        return view('hr::posts.create', $data);
    }

    public function store(CreateHrPostRequest $request)
    {
        $input = $this->prepareInput($request->all(), $request);

        $this->hrPostRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_posts.singular')]));

        return redirect(route('hr.posts.index'));
    }

    public function show($id)
    {
        $post = $this->hrPostRepository->find($id);

        if (empty($post)) {
            flash()->error(__('hr::models/hr_posts.singular').' '.__('messages.not_found'));

            return redirect(route('hr.posts.index'));
        }

        return view('hr::posts.show', compact('post'));
    }

    public function edit($id)
    {
        $data['post'] = $this->hrPostRepository->find($id);

        if (empty($data['post'])) {
            flash()->error(__('hr::models/hr_posts.singular').' '.__('messages.not_found'));

            return redirect(route('hr.posts.index'));
        }

        $data['types'] = $this->hrPostRepository->types();
        $data['statuses'] = $this->hrPostRepository->statuses();
        $data['flages'] = $this->hrPostRepository->flages();
        $data['employees'] = $this->hrPostRepository->employees();
        $data['departments'] = $this->hrPostRepository->departments();
        $data['branches'] = $this->hrPostRepository->branches();

        return view('hr::posts.edit', $data);
    }

    public function update($id, UpdateHrPostRequest $request)
    {
        $post = $this->hrPostRepository->find($id);

        if (empty($post)) {
            flash()->error(__('hr::models/hr_posts.singular').' '.__('messages.not_found'));

            return redirect(route('hr.posts.index'));
        }

        $input = $this->prepareInput($request->all(), $request, $post);

        $this->hrPostRepository->update($input, $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_posts.singular')]));

        return redirect(route('hr.posts.index'));
    }

    public function destroy($id)
    {
        $post = $this->hrPostRepository->find($id);

        if (empty($post)) {
            flash()->error(__('hr::models/hr_posts.singular').' '.__('messages.not_found'));

            return redirect(route('hr.posts.index'));
        }

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $this->hrPostRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_posts.singular')]));

        return redirect(route('hr.posts.index'));
    }

    protected function prepareInput(array $input, Request $request, ?HrPost $existing = null): array
    {
        $input['created_by'] = auth()->id();
        $input['is_pinned'] = $request->boolean('is_pinned');

        $flage = (int) ($input['flage'] ?? HrPost::FLAG_ALL);
        if ($flage === HrPost::FLAG_ALL) {
            $input['employee_id'] = null;
            $input['department_id'] = null;
            $input['branch_id'] = null;
        } elseif ($flage === HrPost::FLAG_EMPLOYEES) {
            $input['department_id'] = null;
            $input['branch_id'] = null;
        } elseif ($flage === HrPost::FLAG_DEPARTMENT) {
            $input['employee_id'] = null;
            $input['branch_id'] = null;
        } elseif ($flage === HrPost::FLAG_BRANCHES) {
            $input['employee_id'] = null;
            $input['department_id'] = null;
        }

        if ((int) ($input['status'] ?? HrPost::STATUS_DRAFT) === HrPost::STATUS_PUBLISHED && empty($input['published_at'])) {
            $input['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            if ($existing?->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $input['image_path'] = $request->file('image')->store('hr/posts', 'public');
        }

        unset($input['image']);

        return $input;
    }
}
