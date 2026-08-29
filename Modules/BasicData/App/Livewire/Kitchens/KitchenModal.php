<?php

namespace Modules\BasicData\App\Livewire\Kitchens;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\BasicData\App\Livewire\Concerns\HasModalForm;
use Modules\BasicData\App\Repositories\DbKitchenRepository;

class KitchenModal extends Component
{
    use HasModalForm;

    public string $barcode = '';
    public int $status = 1;

    protected DbKitchenRepository $repository;

    public function boot(DbKitchenRepository $repository): void
    {
        $this->repository = $repository;
    }

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

    public function mount(): void
    {
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->resetModalState();
        $this->initTranslations();
        $this->barcode = '';
        $this->status = 1;
    }

    #[On('openEditModal')]
    public function openEdit($id): void
    {
        $this->resetFields();
        $kitchen = $this->repository->find($id);
        if ($kitchen) {
            $this->model_id = (int)$id;
            $this->is_edit = true;
            $this->populateTranslations($kitchen);
            $this->barcode = (string)($kitchen->barcode ?? '');
            $this->status = (int)$kitchen->status;
            $this->openModal();
        }
    }

    public function save()
    {
        $this->validate();

        $data = $this->formatTranslations($this->name);
        $data['barcode'] = $this->barcode ?: null;
        $data['status'] = $this->status;

        return $this->saveRecord($data, 'basicdata::models/db_kitchens.singular', 'basicdata.kitchens.index');
    }

    public function render()
    {
        return view('basicdata::livewire.kitchens.kitchen-modal');
    }
}
