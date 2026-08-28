<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('journal_entries', 'source')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->string('source', 50)->default('manual')->after('entry_type')->index();
            });
        }

        // Backfill existing records based on reference_type or description
        try {
            DB::table('journal_entries')
                ->where(function ($q) {
                    $q->where('reference_type', 'like', '%SalesInvoice%')
                      ->orWhere('reference_type', 'like', '%Sales%');
                })
                ->where('reference_type', 'not like', '%PosSession%')
                ->update(['source' => 'sales']);

            DB::table('journal_entries')
                ->where(function ($q) {
                    $q->where('reference_type', 'like', '%PurchaseInvoice%')
                      ->orWhere('reference_type', 'like', '%Purchase%');
                })
                ->update(['source' => 'purchases']);

            DB::table('journal_entries')
                ->where(function ($q) {
                    $q->where('reference_type', 'like', '%Store%')
                      ->orWhere('reference_type', 'like', '%StReceiving%')
                      ->orWhere('reference_type', 'like', '%StIssuing%')
                      ->orWhere('reference_type', 'like', '%StSettlement%')
                      ->orWhere('reference_type', 'like', '%Stock%');
                })
                ->update(['source' => 'store']);

            DB::table('journal_entries')
                ->where('reference_type', 'like', '%Vehicle%')
                ->update(['source' => 'vehicles']);

            DB::table('journal_entries')
                ->where(function ($q) {
                    $q->where('reference_type', 'like', '%Driver%')
                      ->orWhere('reference_type', 'like', '%DrLedger%');
                })
                ->update(['source' => 'drivers']);

            DB::table('journal_entries')
                ->where(function ($q) {
                    $q->where('reference_type', 'like', '%Hr%')
                      ->orWhere('reference_type', 'like', '%Payroll%')
                      ->orWhere('reference_type', 'like', '%EndService%');
                })
                ->update(['source' => 'hr']);

            DB::table('journal_entries')
                ->where(function ($q) {
                    $q->where('reference_type', 'like', '%FncBond%')
                      ->orWhere('reference_type', 'like', '%Finance%');
                })
                ->update(['source' => 'finance']);

            DB::table('journal_entries')
                ->where('reference_type', 'like', '%Asset%')
                ->update(['source' => 'assets']);

            DB::table('journal_entries')
                ->where('reference_type', 'like', '%Pos%')
                ->update(['source' => 'pos']);

            DB::table('journal_entries')
                ->whereIn('entry_type', [2, 3]) // Opening or Closing
                ->whereNull('reference_type')
                ->update(['source' => 'closing']);
        } catch (\Throwable $e) {
            // Ignore backfill errors if any
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('journal_entries', 'source')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
