<?php

namespace Modules\BasicData\App\Livewire\ServicePoints;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Models\DbServicePoint;

class ServicePointModal extends Component
{
    public $service_point_id = null;
    public $is_edit = false;

    public $name = [];
    public $code = '';
    public $type = 1;
    public $status = 1;

    protected function rules(): array
    {
        $rules = [
            'code' => 'nullable|string|max:50',
            'type' => 'required',
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
        $this->service_point_id = null;
        $this->is_edit = false;
        $this->name = [];
        foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
            $this->name[$locale] = '';
        }
        $this->code = '';
        $this->type = 1;
        $this->status = 1;
        $this->resetErrorBag();
    }

    #[On('openCreateModal')]
    public function openCreate()
    {
        $this->resetFields();
        $this->dispatch('open-service-point-modal');
    }

    #[On('openEditModal')]
    public function openEdit($id)
    {
        $this->resetFields();
        $sp = DbServicePoint::find($id);
        if ($sp) {
            $this->service_point_id = $id;
            $this->is_edit = true;
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $sp->translate($locale)->name ?? '';
            }
            $this->code = $sp->code;
            $this->type = $sp->type;
            $this->status = $sp->status;
            $this->dispatch('open-service-point-modal');
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'code' => $this->code,
            'type' => $this->type,
            'status' => $this->status,
        ];

        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }

        if ($this->is_edit && $this->service_point_id) {
            $sp = DbServicePoint::findOrFail($this->service_point_id);
            $sp->update($data);
            $this->dispatch('service-point-saved', message: 'تم تعديل نقطة الخدمة بنجاح!');
        } else {
            DbServicePoint::create($data);
            $this->dispatch('service-point-saved', message: 'تم إضافة نقطة الخدمة بنجاح!');
        }

        $this->resetFields();
        $this->dispatch('close-service-point-modal');
    }

    public function render()
    {
        return view('basicdata::livewire.service-points.service-point-modal');
    }
}
