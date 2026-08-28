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
        Schema::create('pos_device_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('device_uuid'); // Link to pos_devices.uuid
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('token_id')->nullable()->index(); // Sanctum token ID reference if needed
            $table->string('browser_fingerprint')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_name')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('browser')->nullable();
            $table->timestamp('login_time')->useCurrent();
            $table->timestamp('logout_time')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive, Revoked
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index for fast lookup by device UUID
            $table->index('device_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_device_sessions');
    }
};
