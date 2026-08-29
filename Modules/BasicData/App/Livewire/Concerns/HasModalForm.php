<?php

namespace Modules\BasicData\App\Livewire\Concerns;

use Livewire\Attributes\On;

trait HasModalForm
{
    public bool $isOpen = false;
    public bool $is_edit = false;
    public ?int $model_id = null;
    public array $name = [];

    /**
     * Initialize multilingual name fields
     */
    protected function initTranslations(array $fields = ['name']): void
    {
        $locales = array_keys(config('langs', ['ar' => 'Arabic', 'en' => 'English']));
        foreach ($locales as $locale) {
            $this->name[$locale] = '';
        }
    }

    /**
     * Populate multilingual fields from translatable model
     */
    protected function populateTranslations($model, string $field = 'name'): void
    {
        $locales = array_keys(config('langs', ['ar' => 'Arabic', 'en' => 'English']));
        foreach ($locales as $locale) {
            $this->name[$locale] = $model->translate($locale)?->{$field} ?? '';
        }
    }

    /**
     * Format multilingual array for repository saving
     */
    protected function formatTranslations(array $translations, string $key = 'name'): array
    {
        $formatted = [];
        foreach ($translations as $locale => $value) {
            $formatted[$locale] = [$key => $value];
        }
        return $formatted;
    }

    /**
     * Open the modal
     */
    public function openModal(): void
    {
        $this->isOpen = true;
    }

    /**
     * Close the modal and reset errors
     */
    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    /**
     * Reset base modal state
     */
    protected function resetModalState(): void
    {
        $this->model_id = null;
        $this->is_edit = false;
        $this->resetErrorBag();
    }

    /**
     * Open create modal
     */
    #[On('openCreateModal')]
    public function openCreate(): void
    {
        $this->resetFields();
        $this->openModal();
    }

    /**
     * Common save pipeline with flash and SPA redirect
     */
    protected function saveRecord(array $data, string $modelTranslationKey, string $redirectRoute, array $routeParams = [])
    {
        if ($this->is_edit && $this->model_id) {
            if (method_exists($this->repository, 'updateWithRelations')) {
                $this->repository->updateWithRelations($data, $this->model_id);
            } else {
                $this->repository->update($data, $this->model_id);
            }
            flash()->success(__('messages.updated', ['model' => __($modelTranslationKey)]));
        } else {
            if (method_exists($this->repository, 'createWithRelations')) {
                $this->repository->createWithRelations($data);
            } else {
                $this->repository->create($data);
            }
            flash()->success(__('messages.saved', ['model' => __($modelTranslationKey)]));
        }

        $this->closeModal();
        return $this->redirect(route($redirectRoute, $routeParams), navigate: true);
    }
}
