<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            // إضافة الحقول الجديدة
           
            $table->string('license_reg_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {


            // حذف الحقول الجديدة
            $table->dropColumn([
                'license_reg_type'
            ]);
        });
    }
};
