<?php

namespace Modules\BasicData\App\Livewire\Units;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Livewire\Concerns\HasModalForm;
use Modules\BasicData\App\Repositories\DbUnitRepository;

class UnitModal extends Component
{
    use HasModalForm;

    public $status = 1;

    protected $repository;

    public function boot(DbUnitRepository $repository): void
    {
        $this->repository = $repository;
    }

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
        $unit = $this->repository->find($id);
        if ($unit) {
            $this->model_id = $id;
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

        if ($this->is_edit && $this->model_id) {
            $this->repository->update($data, $this->model_id);
            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_units.singular')]));
        } else {
            $this->repository->create($data);
            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_units.singular')]));
        }

        $this->closeModal();
        return $this->redirect(route('basicdata.units.index'), navigate: true);
    }

    public function render()
    {
        return view('basicdata::livewire.units.unit-modal');
    }
}
