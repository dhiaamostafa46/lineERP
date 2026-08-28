<?php

namespace Modules\BasicData\App\Livewire\Kitchens;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Models\DbKitchen;

class KitchenModal extends Component
{
    public $isOpen = false;
    public $kitchen_id = null;
    public $is_edit = false;

    public $name = [];
    public $barcode = '';
    public $status = 1;

    protected function rules(): array
    {
        $rules = [
            'barcode' => 'nullable|string|max:50',
            'status' => 'required',
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
        $this->kitchen_id = null;
        $this->is_edit = false;
        $this->name = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
        }
        $this->barcode = '';
        $this->status = 1;
        $this->resetErrorBag();
    }

    #[On('openCreateModal')]
    public function openCreate()
    {
        $this->resetFields();
        $this->isOpen = true;
    }

    #[On('openEditModal')]
    public function openEdit($id)
    {
        $this->resetFields();
        $kitchen = DbKitchen::find($id);
        if ($kitchen) {
            $this->kitchen_id = $id;
            $this->is_edit = true;
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $kitchen->translate($locale)->name ?? '';
            }
            $this->barcode = $kitchen->barcode;
            $this->status = $kitchen->status;
            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'barcode' => $this->barcode,
            'status' => $this->status,
        ];

        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }

        if ($this->is_edit && $this->kitchen_id) {
            $kitchen = DbKitchen::findOrFail($this->kitchen_id);
            $kitchen->update($data);
            session()->flash('message', 'تم تعديل المطبخ بنجاح!');
        } else {
            DbKitchen::create($data);
            session()->flash('message', 'تم إضافة المطبخ بنجاح!');
        }

        $this->closeModal();
        flash()->success($this->is_edit ? 'تم تعديل المطبخ بنجاح!' : 'تم إضافة المطبخ بنجاح!');
        return $this->redirect(route('basicdata.kitchens.index'), navigate: true);
    }

    public function render()
    {
        return view('basicdata::livewire.kitchens.kitchen-modal');
    }
}
