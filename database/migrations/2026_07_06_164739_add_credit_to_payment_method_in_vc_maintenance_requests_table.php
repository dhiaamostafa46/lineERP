<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safe modification using raw SQL for ENUMs in MySQL
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE vc_maintenance_requests MODIFY COLUMN payment_method ENUM('cash','bank','insurance','credit') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse back to the original ENUMs
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE vc_maintenance_requests MODIFY COLUMN payment_method ENUM('cash','bank','insurance') DEFAULT NULL");
    }
};
