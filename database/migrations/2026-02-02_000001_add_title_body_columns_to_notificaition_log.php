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
        Schema::table('notification_log_items', function (Blueprint $table) {
            $table->string('title')->nullable()->after('notifiable_id');
            $table->string('body')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('notification_log_items', function (Blueprint $table) {
            $table->string('title')->nullable()->after('notifiable_id');
            $table->string('body')->nullable()->after('title');
        });
    }
};
