<?php

namespace Modules\BasicData\App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\BasicData\App\Livewire\Concerns\HasModalForm;
use Modules\BasicData\App\Repositories\DbCategoryRepository;

class CategoryModal extends Component
{
    use WithFileUploads, HasModalForm;

    public $parent_id = null;
    public $status = 1;
    public $type = 1;
    public $img;
    public $existing_img = null;

    protected $repository;

    public function boot(DbCategoryRepository $repository): void
    {
        $this->repository = $repository;
    }

    protected function rules(): array
    {
        $rules = [
            'status' => 'required',
            'type' => 'nullable',
            'parent_id' => 'nullable',
            'img' => 'nullable|image|max:2048',
        ];

        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $rules["name.$locale"] = 'required|string|max:255';
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->resetModalState();
        $this->initTranslations();
        $this->parent_id = null;
        $this->status = 1;
        $this->type = 1;
        $this->img = null;
        $this->existing_img = null;
    }

    #[On('openCreateModal')]
    public function openCreate(): void
    {
        $this->resetFields();
        $this->openModal();
    }

    #[On('openEditModal')]
    public function openEdit($id): void
    {
        $this->resetFields();
        $category = $this->repository->find($id);
        if ($category) {
            $this->model_id = $id;
            $this->is_edit = true;
            $this->populateTranslations($category);
            $this->parent_id = $category->parent_id;
            $this->status = $category->status;
            $this->type = $category->type ?? 1;
            $this->existing_img = $category->imgThumbPath;
            $this->openModal();
        }
    }

    public function save()
    {
        $this->validate();

        $data = $this->formatTranslations($this->name);
        $data['status'] = $this->status;
        $data['type'] = $this->type;
        $data['parent_id'] = $this->parent_id ?: null;

        if ($this->img) {
            $data['img'] = $this->img;
        }

        if ($this->is_edit && $this->model_id) {
            $this->repository->update($data, $this->model_id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_categories.singular')]));
        } else {
            $this->repository->create($data);
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_categories.singular')]));
        }

        $this->closeModal();
        return $this->redirect(route('basicdata.categories.index'), navigate: true);
    }

    public function render()
    {
        return view('basicdata::livewire.categories.category-modal', [
            'parentCategories' => $this->repository->parentCategories($this->model_id),
        ]);
    }
}
