<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;

class ProjectService
{
    public function __construct(public ProjectRepository $projectRepository) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Project
    {
        $project = new Project;
        $this->fillAttributes($project, $data);

        if (auth()->check()) {
            $project->created_by = auth()->id();
            $project->updated_by = auth()->id();
        }

        $project->save();
        $this->syncTranslations($project, $data);
        $project->save();

        return $project->fresh(['translations', 'company']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Project $project, array $data): Project
    {
        $this->fillAttributes($project, $data);
        $this->syncTranslations($project, $data);

        if (auth()->check()) {
            $project->updated_by = auth()->id();
        }

        $project->save();

        return $project->fresh(['translations', 'company']);
    }

    public function delete(Project $project): bool
    {
        if (auth()->check()) {
            $project->deleted_by = auth()->id();
            $project->save();
        }

        return (bool) $this->projectRepository->delete($project->id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fillAttributes(Project $project, array $data): void
    {
        $project->company_id = (int) $data['company_id'];

        $code = trim((string) ($data['code'] ?? ''));
        if ($code !== '') {
            $project->code = $code;
        } elseif (! $project->exists) {
            $project->code = Project::generateCode((int) $data['company_id']);
        }

        $project->status = (int) $data['status'];
        $project->start_date = $data['start_date'] ?? null;
        $project->end_date = $data['end_date'] ?? null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncTranslations(Project $project, array $data): void
    {
        foreach (config('langs') as $locale => $_language) {
            $project->translateOrNew($locale)->name = $data[$locale]['name'];
        }
    }
}
