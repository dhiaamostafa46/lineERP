<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('api_req_resp')) {
            Schema::create('api_req_resp', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
                $table->integer('tenant_id')->nullable();
                $table->string('method')->nullable();
                $table->string('endpoint');
                $table->text('_request')->nullable();
                $table->text('_response')->nullable();
                $table->string('status')->nullable();
                $table->string('duration_ms')->nullable();
                $table->ipAddress('ip')->nullable();
                $table->timestamps();
            });
        }

    }
    public function down(): void
    {
        Schema::dropIfExists('api_req_resp');
    }

};
