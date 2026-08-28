<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pos_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_sessions', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->unique();
            }
        });

        Schema::table('pos_session_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_session_transactions', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->unique();
            }
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_invoices', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id')->unique();
            }
        });

        // Fill UUIDs for existing records
        $sessions = DB::table('pos_sessions')->whereNull('uuid')->get();
        foreach ($sessions as $session) {
            DB::table('pos_sessions')->where('id', $session->id)->update(['uuid' => Str::uuid()]);
        }

        $transactions = DB::table('pos_session_transactions')->whereNull('uuid')->get();
        foreach ($transactions as $transaction) {
            DB::table('pos_session_transactions')->where('id', $transaction->id)->update(['uuid' => Str::uuid()]);
        }
        
        $invoices = DB::table('sales_invoices')->whereNull('uuid')->get();
        foreach ($invoices as $invoice) {
            DB::table('sales_invoices')->where('id', $invoice->id)->update(['uuid' => Str::uuid()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sessions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('pos_session_transactions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
