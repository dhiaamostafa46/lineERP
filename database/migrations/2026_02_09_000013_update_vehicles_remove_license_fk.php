<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                // حذف المفتاح الأجنبي
                $table->dropForeign(['vehicle_license_id']);
                // حذف العمود
                $table->dropColumn('vehicle_license_id');
            }

            // إضافة الحقول الجديدة
            $table->string('license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('license_image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            // إعادة العمود
            $table->foreignId('vehicle_license_id')->nullable();

            $table->foreign('vehicle_license_id')
                ->references('id')
                ->on('vehicle_licenses')
                ->nullOnDelete();

            // حذف الحقول الجديدة
            $table->dropColumn([
                'license_number',
                'license_expiry_date',
                'license_image'
            ]);
        });
    }
};
