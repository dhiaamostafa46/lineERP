<?php

namespace Modules\HR\App\Livewire\Profile;

use Modules\HR\App\Models\HrTask;

class Tasks extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrTask::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.tasks';
}
