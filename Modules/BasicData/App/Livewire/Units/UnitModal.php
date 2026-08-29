<?php

namespace Modules\BasicData\App\Livewire\Units;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Livewire\Concerns\HasModalForm;
use Modules\BasicData\App\Repositories\DbUnitRepository;

class UnitModal extends Component
{
    use HasModalForm;

    public int $status = 1;
    protected DbUnitRepository $repository;

    public function boot(DbUnitRepository $repository): void
    {
        $this->repository = $repository;
    }

    protected function rules(): array
    {
        $rules = ['status' => 'required'];
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
        $this->status = 1;
    }

    #[On('openEditModal')]
    public function openEdit($id): void
    {
        $this->resetFields();
        $unit = $this->repository->find($id);
        if ($unit) {
            $this->model_id = (int)$id;
            $this->is_edit = true;
            $this->populateTranslations($unit);
            $this->status = (int)$unit->status;
            $this->openModal();
        }
    }

    public function save()
    {
        $this->validate();
        $data = $this->formatTranslations($this->name);
        $data['status'] = $this->status;

        return $this->saveRecord($data, 'basicdata::models/db_units.singular', 'basicdata.units.index');
    }

    public function render()
    {
        return view('basicdata::livewire.units.unit-modal');
    }
}
