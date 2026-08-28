<?php

namespace Modules\HR\App\Livewire\Profile;

use Modules\HR\App\Models\HrCustody;

class Custody extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrCustody::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.custody';
}
