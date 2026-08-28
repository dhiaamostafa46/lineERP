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
        Schema::table('st_direct_transfer_items', function (Blueprint $table) {
            $table->decimal('received_quantity', 18, 4)->default(0)->after('quantity');
            $table->decimal('variance_quantity', 18, 4)->default(0)->after('received_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('st_direct_transfer_items', function (Blueprint $table) {
            $table->dropColumn(['received_quantity', 'variance_quantity']);
        });
    }
};
