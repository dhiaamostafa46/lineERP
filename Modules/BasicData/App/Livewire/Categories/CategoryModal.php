<?php

namespace Modules\BasicData\App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\BasicDataApp\Category;
use Modules\BasicData\App\Repositories\DbCategoryRepository;

class CategoryModal extends Component
{
    use WithFileUploads;

    public $category_id = null;
    public $is_edit = false;

    public $name = [];
    public $parent_id = null;
    public $status = 1;
    public $type = 1;
    public $img;
    public $existing_img = null;

    protected function rules(): array
    {
        $rules = [
            'status' => 'required',
            'type' => 'nullable',
            'parent_id' => 'nullable|exists:categories,id',
            'img' => 'nullable|image|max:2048',
        ];

        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $rules["name.$locale"] = 'required|string|max:255';
        }

        return $rules;
    }

    public function mount()
    {
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->category_id = null;
        $this->is_edit = false;
        $this->name = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
        }
        $this->parent_id = null;
        $this->status = 1;
        $this->type = 1;
        $this->img = null;
        $this->existing_img = null;
        $this->resetErrorBag();
    }

    #[On('openCreateModal')]
    public function openCreate()
    {
        $this->resetFields();
        $this->dispatch('open-category-modal');
    }

    #[On('openEditModal')]
    public function openEdit($id)
    {
        $this->resetFields();
        $category = Category::find($id);
        if ($category) {
            $this->category_id = $id;
            $this->is_edit = true;
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $category->translate($locale)->name ?? '';
            }
            $this->parent_id = $category->parent_id;
            $this->status = $category->status;
            $this->type = $category->type ?? 1;
            $this->existing_img = $category->imgThumbPath;
            $this->dispatch('open-category-modal');
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'status' => $this->status,
            'type' => $this->type,
            'parent_id' => $this->parent_id ?: null,
        ];

        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }

        if ($this->is_edit && $this->category_id) {
            $category = Category::findOrFail($this->category_id);
            $category->update($data);

            if ($this->img) {
                $category->clearMediaCollection('categories');
                $category->addMedia($this->img->getRealPath())->toMediaCollection('categories');
            }

            $this->dispatch('category-saved', message: 'تم تعديل التصنيف بنجاح!');
        } else {
            $category = Category::create($data);

            if ($this->img) {
                $category->addMedia($this->img->getRealPath())->toMediaCollection('categories');
            }

            $this->dispatch('category-saved', message: 'تم إضافة التصنيف بنجاح!');
        }

        $this->resetFields();
        $this->dispatch('close-category-modal');
    }

    public function render()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->when($this->category_id, fn($q) => $q->where('id', '!=', $this->category_id))
            ->get();

        return view('basicdata::livewire.categories.category-modal', [
            'parentCategories' => $parentCategories,
        ]);
    }
}
