<?php

namespace Modules\AccuSoft\App\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\AccuSoft\App\Models\Asset;
use App\Services\AccuSoft\AssetService;

class GenerateMissingDepreciationSchedules extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'accusoft:generate-missing-depreciation-schedules';

    /**
     * The console command description.
     */
    protected $description = 'Generates missing depreciation schedules for existing assets';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(AssetService $assetService)
    {
        $this->info('Starting to generate missing depreciation schedules...');
        
        $assets = Asset::where('calculation_type', 'automatic')
            ->where('depreciation_status', '!=', Asset::DEPRECIATION_STATUS_NONE)
            ->where('status', '!=', Asset::STATUS_DISPOSED)
            ->get();

        $count = 0;
        foreach ($assets as $asset) {
            // Check if schedule is fully generated. We know an asset should have `useful_life` periods.
            $existingCount = $asset->depreciations()->count();
            if ($existingCount < $asset->useful_life) {
                $assetService->generateDepreciationSchedule($asset);
                $this->line("Generated schedule for asset: {$asset->code}");
                $count++;
            }
        }

        $this->info("Completed! Generated schedules for {$count} assets.");
    }

}
