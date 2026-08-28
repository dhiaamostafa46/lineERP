<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessFailedInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:process-failed 
                            {--hours=24 : The minimum age of the invoice in hours}
                            {--device= : Specific POS Device UUID}
                            {--pos-only : Process only POS invoices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess invoices that failed ZATCA reporting or Journal Entry creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $deviceUuid = $this->option('device');
        $posOnly = $this->option('pos-only');

        $cutoffDate = now()->subHours($hours);

        $query = \App\Models\invApp\SalesInvoice::query()
            ->where('created_at', '<=', $cutoffDate)
            ->where(function ($q) {
                $q->whereIn('status', [
                    \App\Models\invApp\SalesInvoice::STATUS_DRAFT,
                    \App\Models\invApp\SalesInvoice::STATUS_SUBMITTED,
                    \App\Models\invApp\SalesInvoice::STATUS_REJECTED
                ])
                ->orWhereNotNull('zatca_errors')
                ->orWhereNull('journal_entry_id');
            });

        if ($posOnly) {
            $query->whereIn('type_inv', [\App\Models\invApp\SalesInvoice::TYPE_POS, \App\Models\invApp\SalesInvoice::TYPE_RETURN_POS]);
        }

        if ($deviceUuid) {
            $device = \Modules\Pos\App\Models\PosDevice::where('uuid', $deviceUuid)->first();
            if (!$device) {
                $this->error("POS Device not found with UUID: $deviceUuid");
                return Command::FAILURE;
            }
            
            $sessionIds = \Modules\Pos\App\Models\PosSession::where('device_id', $device->id)->pluck('id');
            $query->whereIn('pos_session_id', $sessionIds);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            $this->info("No failed invoices found older than {$hours} hours.");
            return Command::SUCCESS;
        }

        $this->info("Found {$invoices->count()} invoices to process.");

        $repository = app(\Modules\Invoices\App\Repositories\SalesInvoiceRepository::class);

        $successCount = 0;
        $failCount = 0;

        foreach ($invoices as $invoice) {
            $this->info("Processing Invoice ID: {$invoice->id} (Invoice #: {$invoice->invoice_number})");
            try {
                $success = $repository->retrySalesLinking($invoice);
                if ($success) {
                    $successCount++;
                    $this->info("  - Successfully processed.");
                } else {
                    $failCount++;
                    $this->error("  - Skipped or partially failed.");
                }
            } catch (\Exception $e) {
                $failCount++;
                $this->error("  - Exception: " . $e->getMessage());
                \Illuminate\Support\Facades\Log::error("Failed to process invoice ID {$invoice->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }

        $this->info("Done. Success: $successCount, Failed: $failCount");
        return Command::SUCCESS;
    }
}
