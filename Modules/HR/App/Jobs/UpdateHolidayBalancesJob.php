<?php

namespace Modules\HR\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\HR\App\Repositories\HrHolidayBalanceRepository;

class UpdateHolidayBalancesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // This logic is no longer needed in the new dynamic balance architecture.
        // Balances are calculated dynamically in HrHolidayBalanceRepository->FindBalance.
    }
}
