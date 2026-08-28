<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_identities', function (Blueprint $table) {
            // إضافة الحقول الجديدة

            $table->unsignedTinyInteger('owner_type')->default(1)->comment('1=employee,2=driver');
            $table->unsignedTinyInteger('identity_type')->nullable()->comment('1 => identity, 2 => residence, 3 => driving_license, 4 => passport')->change();
            $table->string('license_type')->nullable();
            $table->string('file')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employee_identities', function (Blueprint $table) {
            // حذف الحقول الجديدة
            $table->dropColumn(['owner_type', 'license_type', 'file']);
            $table->unsignedTinyInteger('identity_type')->nullable()->comment('1 => identity, 2 => residence')->change();
        });
    }
};
