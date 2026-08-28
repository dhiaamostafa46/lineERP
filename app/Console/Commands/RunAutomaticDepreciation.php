<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AccuSoft\AssetService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class RunAutomaticDepreciation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accusoft:run-depreciation {month?} {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the automatic monthly depreciation for active assets in the AccuSoft module';

    protected AssetService $assetService;

    /**
     * Execute the console command.
     */
    public function handle(AssetService $assetService)
    {
        $this->assetService = $assetService;

        $month = $this->argument('month') ?? now()->month;
        $year = $this->argument('year') ?? now()->year;

        $this->info("Starting automated depreciation run for $month/$year...");
        Log::info("Automated depreciation run started for $month/$year.");

        try {
            // Find a system user or use ID 1 for automated tasks
            $systemUserId = 1;

            $run = $this->assetService->batchDepreciationRun(
                (int) $month, 
                (int) $year, 
                $systemUserId, 
                'تم تنفيذ الإهلاك تلقائياً بواسطة النظام (Automated Run)',
                false
            );

            $this->info("Depreciation run completed successfully! Run ID: {$run->id}, Total Depreciation: {$run->total_depreciation}");
            Log::info("Automated depreciation run completed. Run ID: {$run->id}");
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error("Depreciation run failed: " . $e->getMessage());
            Log::error("Automated depreciation run failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
