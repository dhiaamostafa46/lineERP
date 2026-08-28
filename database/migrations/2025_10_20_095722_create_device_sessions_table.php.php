<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('device_sessions')) {
            Schema::create('device_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('org_id')->nullable();
                $table->string('device_token')->nullable();
                $table->string('device_serial')->nullable();
                $table->string('device_name')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('device_ip')->nullable();
                  $table->string('ip')->nullable();
                $table->string('device_type')->nullable(); // desktop, laptop, mobile
                $table->string('browser')->nullable();
                $table->string('os')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'device_serial']);
            });
        }
    }

    public function down() {
        Schema::dropIfExists('device_sessions');
    }
};








