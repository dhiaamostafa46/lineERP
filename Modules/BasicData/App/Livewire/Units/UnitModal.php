<?php

namespace Modules\BasicData\App\Livewire\Units;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\BasicDataApp\Unit;

class UnitModal extends Component
{
    public $isOpen = false;
    public $unit_id = null;
    public $is_edit = false;

    public $name = [];
    public $status = 1;

    protected function rules(): array
    {
        $rules = [
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
        $this->unit_id = null;
        $this->is_edit = false;
        $this->name = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
        }
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
        $unit = Unit::find($id);
        if ($unit) {
            $this->unit_id = $id;
            $this->is_edit = true;
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $unit->translate($locale)->name ?? '';
            }
            $this->status = $unit->status;
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
            'status' => $this->status,
        ];

        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }

        if ($this->is_edit && $this->unit_id) {
            $unit = Unit::findOrFail($this->unit_id);
            $unit->update($data);
            session()->flash('message', 'تم تعديل الوحدة بنجاح!');
        } else {
            Unit::create($data);
            session()->flash('message', 'تم إضافة الوحدة بنجاح!');
        }

        $this->closeModal();
        return redirect()->route('basicdata.units.index');
    }

    public function render()
    {
        return view('basicdata::livewire.units.unit-modal');
    }
}
