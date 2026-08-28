<?php

namespace Modules\HR\App\Livewire\Profile;

use Modules\HR\App\Models\HrPenalty;

class Penalties extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrPenalty::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.penalties';
}

