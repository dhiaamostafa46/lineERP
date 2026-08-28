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
        Schema::table('zatca_settings', function (Blueprint $table) {
            $table->string('cv')->nullable();
            $table->string('activity_classification')->nullable();
            $table->string('registered_address')->nullable();
            $table->string('otp')->nullable();
            $table->string('otp_confirmation')->nullable();
            $table->string('status')->nullable();
            
            // Hidden fields
            $table->string('serial_number')->default('Evix|ver-1.0|Evix-ORG-2320250728090713')->nullable();
            $table->text('prk')->nullable();
            $table->text('prod_secret')->nullable();
            $table->string('request_id')->nullable();
            $table->text('csr_response_binary_token')->nullable();
            $table->string('inv_type')->default('1100')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zatca_settings', function (Blueprint $table) {
            $table->dropColumn([
                'cv',
                'activity_classification',
                'registered_address',
                'otp',
                'otp_confirmation',
                'status',
                'serial_number',
                'prk',
                'prod_secret',
                'request_id',
                'csr_response_binary_token',
                'inv_type'
            ]);
        });
    }
};
