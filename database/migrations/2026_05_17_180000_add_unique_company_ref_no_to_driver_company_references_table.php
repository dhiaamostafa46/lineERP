<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_company_references', function (Blueprint $table) {
            $table->unique(['company_id', 'ref_no'], 'driver_company_references_company_ref_unique');
        });
    }

    public function down(): void
    {
        Schema::table('driver_company_references', function (Blueprint $table) {
            $table->dropUnique('driver_company_references_company_ref_unique');
        });
    }
};
