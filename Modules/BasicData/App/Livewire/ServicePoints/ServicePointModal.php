<?php

namespace Modules\BasicData\App\Livewire\ServicePoints;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Repositories\DbServicePointRepository;

class ServicePointModal extends Component
{
    public $isOpen = false;
    public $service_point_id = null;
    public $is_edit = false;

    public $name = [];
    public $code = '';
    public $type = 1;
    public $status = 1;

    protected $repository;

    public function boot(DbServicePointRepository $repository)
    {
        $this->repository = $repository;
    }

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
        $this->isOpen = true;
    }

    #[On('openEditModal')]
    public function openEdit($id)
    {
        $this->resetFields();
        $sp = $this->repository->find($id);
        if ($sp) {
            $this->service_point_id = $id;
            $this->is_edit = true;
            foreach (config('langs', ['ar' => 'Arabic', 'en' => 'English']) as $locale => $name) {
                $this->name[$locale] = $sp->translate($locale)->name ?? '';
            }
            $this->code = $sp->code;
            $this->type = $sp->type;
            $this->status = $sp->status;
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
            'code' => $this->code,
            'type' => $this->type,
            'status' => $this->status,
        ];

        foreach ($this->name as $locale => $val) {
            $data[$locale] = ['name' => $val];
        }

        if ($this->is_edit && $this->service_point_id) {
            $this->repository->update($data, $this->service_point_id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_service_points.singular')]));
        } else {
            $this->repository->create($data);
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_service_points.singular')]));
        }

        $this->closeModal();
        return $this->redirect(route('basicdata.service_points.index'), navigate: true);
    }

    public function render()
    {
        return view('basicdata::livewire.service-points.service-point-modal');
    }
}
