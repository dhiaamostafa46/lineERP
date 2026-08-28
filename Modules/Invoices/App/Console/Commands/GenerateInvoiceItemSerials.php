<?php

namespace Modules\Invoices\App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\invApp\SalesInvoice;
use App\Models\invApp\SalesInvoiceItem;

class GenerateInvoiceItemSerials extends Command
{
    protected $signature = 'invoices:generate-serials {--batch=100 : عدد الفواتير في كل دفعة}';

    protected $description = 'معالجة serial للفواتير القديمة التي لم تحتوي على serial';

    public function handle()
    {
        $batchSize = (int) $this->option('batch');
        
        $this->info('🔄 بدء معالجة serial للفواتير القديمة...');

        // احصل على عدد الفواتير التي بحاجة معالجة
        $totalInvoices = SalesInvoice::whereHas('items', function ($q) {
            $q->whereNull('serial');
        })->count();

        if ($totalInvoices === 0) {
            $this->info('✅ جميع الفواتير تحتوي على serial بالفعل.');
            return Command::SUCCESS;
        }

        $this->info("📊 عدد الفواتير المطلوب معالجتها: {$totalInvoices}");

        $progressBar = $this->output->createProgressBar($totalInvoices);
        $progressBar->start();

        SalesInvoice::whereHas('items', function ($q) {
            $q->whereNull('serial');
        })
        ->chunk($batchSize, function ($invoices) use ($progressBar) {
            foreach ($invoices as $invoice) {
                $this->processInvoice($invoice);
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->line('');
        
        $this->info('✅ تمت معالجة جميع الفواتير بنجاح!');
        return Command::SUCCESS;
    }

    protected function processInvoice(SalesInvoice $invoice): void
    {
        $items = $invoice->items()->whereNull('serial')->get();
        
        if ($items->isEmpty()) {
            return;
        }

        $usedSerials = $invoice->items()->whereNotNull('serial')->pluck('serial')->toArray();
        
        foreach ($items as $index => $item) {
            // توليد serial عشوائي 6 أرقام
            do {
                $serial = str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($serial, $usedSerials));
            
            $item->update(['serial' => $serial]);
            $usedSerials[] = $serial;
        }
    }
}

