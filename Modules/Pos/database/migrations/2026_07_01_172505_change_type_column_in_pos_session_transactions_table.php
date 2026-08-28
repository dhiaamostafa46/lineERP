<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum to varchar
        DB::statement("ALTER TABLE pos_session_transactions MODIFY COLUMN type VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back if needed
        DB::statement("ALTER TABLE pos_session_transactions MODIFY COLUMN type ENUM('cash_in', 'cash_out') NOT NULL");
    }
};
