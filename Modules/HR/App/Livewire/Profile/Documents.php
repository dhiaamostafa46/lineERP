<?php

namespace Modules\HR\App\Livewire\Profile;

use Modules\HR\App\Models\HrDocument;

class Documents extends ProfileTab
{
    /** @var string */
    protected string $modelClass = HrDocument::class;
    /** @var string */
    protected string $viewName = 'hr::livewire.profile.documents';
}
