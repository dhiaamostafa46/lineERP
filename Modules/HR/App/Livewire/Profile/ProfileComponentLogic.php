<?php

namespace Modules\HR\App\Livewire\Profile;

use App\Models\Employee;

/**
 * Trait ProfileComponentLogic
 *
 * Handles common logic for profile-related Livewire components,
 * such as initializing employee data.
 */
trait ProfileComponentLogic
{
    /** @var Employee|null */
    public $employee;

    /** @var \Modules\HR\App\Models\HrEmployee|null */
    public $HrEmployee;

    /**
     * Mount component and initialize employee data.
     */
    public function initializeProfile(): void
    {
        $this->employee = auth()->user()->employee;
        $this->HrEmployee = $this->employee?->hrEmployee;
    }
}
