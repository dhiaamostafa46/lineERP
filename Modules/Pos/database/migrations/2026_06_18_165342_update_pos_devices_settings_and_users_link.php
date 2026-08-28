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
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->boolean('is_users_linked')->default(false)->after('is_active');
            $table->json('linked_users')->nullable()->after('is_users_linked');
            
            // Drop unnecessary columns
            $table->dropColumn([
                'session_timeout_minutes',
                'auto_open_drawer',
                'allow_discount_modification',
                'max_discount_percent'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->dropColumn(['is_users_linked', 'linked_users']);
            
            $table->integer('session_timeout_minutes')->default(0);
            $table->boolean('auto_open_drawer')->default(true);
            $table->boolean('allow_discount_modification')->default(true);
            $table->decimal('max_discount_percent', 5, 2)->default(100.00);
        });
    }
};
