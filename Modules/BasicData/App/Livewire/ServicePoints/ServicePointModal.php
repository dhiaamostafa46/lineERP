<?php

namespace Modules\BasicData\App\Livewire\ServicePoints;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Livewire\Concerns\HasModalForm;
use Modules\BasicData\App\Repositories\DbServicePointRepository;

class ServicePointModal extends Component
{
    use HasModalForm;

    public string $code = '';
    public int $type = 1;
    public int $status = 1;

    protected $repository;

    public function boot(DbServicePointRepository $repository): void
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

    public function mount(): void
    {
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->resetModalState();
        $this->initTranslations();
        $this->code = '';
        $this->type = 1;
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
        $sp = $this->repository->find($id);
        if ($sp) {
            $this->model_id = $id;
            $this->is_edit = true;
            $this->populateTranslations($sp);
            $this->code = (string)$sp->code;
            $this->type = (int)$sp->type;
            $this->status = (int)$sp->status;
            $this->openModal();
        }
    }

    public function save()
    {
        $this->validate();

        $data = $this->formatTranslations($this->name);
        $data['code'] = $this->code ?: null;
        $data['type'] = $this->type;
        $data['status'] = $this->status;

        if ($this->is_edit && $this->model_id) {
            $this->repository->update($data, $this->model_id);
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
