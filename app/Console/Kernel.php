<?php

namespace App\Console;
use App\Console\Commands\RecordEmployeePresence;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use InfyOm\Generator\Commands\API\APIGeneratorCommand;
use InfyOm\Generator\Commands\API\APIControllerGeneratorCommand;
use InfyOm\Generator\Commands\API\APIRequestsGeneratorCommand;
use InfyOm\Generator\Commands\APIScaffoldGeneratorCommand;
use InfyOm\Generator\Commands\Scaffold\ScaffoldGeneratorCommand;

use App\Console\Commands\RunAutomaticDepreciation;

use App\Console\Commands\ProcessFailedInvoicesCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        APIGeneratorCommand::class, 
        APIRequestsGeneratorCommand::class, 
        APIControllerGeneratorCommand::class, 
        APIScaffoldGeneratorCommand::class, 
        ScaffoldGeneratorCommand::class, 
        RecordEmployeePresence::class, 
        RunAutomaticDepreciation::class,
        ProcessFailedInvoicesCommand::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        //   $schedule->command('attendance:record')->dailyAt('23:58');
        $schedule->command('attendance:record')->everyMinute();
        $schedule->command('notifications:process')->everyMinute();
        $schedule->command('attendance-policies')->daily();
        
        // Automatic Fixed Assets Depreciation - runs on the last day of the month at 23:50
        $schedule->command('accusoft:run-depreciation')->monthlyOn(28, '23:50');

        // Process any failed or pending invoices older than 24 hours
        $schedule->command('invoices:process-failed --hours=24')->hourly();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
