<?php

namespace Modules\HR\App\Livewire\Profile;

use Modules\HR\App\Models\HrJustification;

class Settlement extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrJustification::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.settlement';
}
