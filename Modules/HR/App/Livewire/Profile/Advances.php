<?php

namespace Modules\HR\App\Livewire\Profile;

use Modules\HR\App\Models\HrAdvance;

class Advances extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrAdvance::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.advances';
}
