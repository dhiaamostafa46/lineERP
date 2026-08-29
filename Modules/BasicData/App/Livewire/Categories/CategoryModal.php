<?php

namespace Modules\BasicData\App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\BasicData\App\Helpers\HasModalForm;
use Modules\BasicData\App\Repositories\DbCategoryRepository;

class CategoryModal extends Component
{
    use WithFileUploads, HasModalForm;

    public $parent_id = null;
    public int $status = 1;
    public int $type = 1;
    public $img;
    public $existing_img = null;

    protected DbCategoryRepository $repository;

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

    #[On('openEditModal')]
    public function openEdit($id): void
    {
        $this->resetFields();
        $category = $this->repository->find($id);
        if ($category) {
            $this->model_id = (int)$id;
            $this->is_edit = true;
            $this->populateTranslations($category);
            $this->parent_id = $category->parent_id;
            $this->status = (int)$category->status;
            $this->type = (int)($category->type ?? 1);
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

        return $this->saveRecord($data, 'basicdata::models/db_categories.singular', 'basicdata.categories.index');
    }

    public function render()
    {
        return view('basicdata::livewire.categories.category-modal', [
            'parentCategories' => $this->repository->parentCategories($this->model_id),
        ]);
    }
}
