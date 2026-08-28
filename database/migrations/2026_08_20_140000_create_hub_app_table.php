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
        Schema::create('hub_app', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->default(1)->index();
            $table->string('app_code')->index();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->longText('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->string('connection_status')->default('active');
            $table->string('webhook_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamps();

            $table->unique(['app_code', 'org_id'], 'hub_app_code_org_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_app');
    }
};
