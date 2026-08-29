<?php

namespace Modules\BasicData\App\Livewire\ServicePoints;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Helpers\HasModalForm;
use Modules\BasicData\App\Repositories\DbServicePointRepository;

class ServicePointModal extends Component
{
    use HasModalForm;

    public string $code = '';
    public int $type = 1;
    public int $status = 1;

    protected DbServicePointRepository $repository;

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

    #[On('openEditModal')]
    public function openEdit($id): void
    {
        $this->resetFields();
        $sp = $this->repository->find($id);
        if ($sp) {
            $this->model_id = (int)$id;
            $this->is_edit = true;
            $this->populateTranslations($sp);
            $this->code = (string)($sp->code ?? '');
            $this->type = (int)($sp->type ?? 1);
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

        return $this->saveRecord($data, 'basicdata::models/db_service_points.singular', 'basicdata.service_points.index');
    }

    public function render()
    {
        return view('basicdata::livewire.service-points.service-point-modal');
    }
}
