<?php

namespace Modules\HR\App\Livewire\Profile;
use Modules\HR\App\Models\HrHoliday;

class Vacations extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrHoliday::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.vacations';
}
