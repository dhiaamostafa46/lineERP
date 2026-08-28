<?php

namespace Modules\BasicData\App\Livewire\Concerns;

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
            $this->name[$locale] = $model->translate($locale)?->name ?? '';
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
}
