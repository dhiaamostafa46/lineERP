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
        Schema::create('company_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            /*
    |--------------------------------------------------------------------------
    | Company Revenue
    |--------------------------------------------------------------------------
    */

            $table->enum('company_pricing_type', ['per_order', 'percentage', 'monthly', 'custom'])->default('per_order');

            $table->decimal('company_pricing_value', 10, 2)->default(0);

            /*
    |--------------------------------------------------------------------------
    | Driver Payment
    |--------------------------------------------------------------------------
    */

            $table->enum('driver_payment_type', ['salary', 'per_order', 'percentage'])->default('per_order');

            $table->decimal('driver_payment_value', 10, 2)->default(0);

            /*
    |--------------------------------------------------------------------------
    | Settlement
    |--------------------------------------------------------------------------
    */

            $table->enum('settlement_cycle', ['daily', 'weekly', 'monthly'])->default('monthly');

            /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();

            /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

            $table->unsignedTinyInteger('status')->default(2)->comment('1=inactive,2=active');

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_contracts');
    }
};
