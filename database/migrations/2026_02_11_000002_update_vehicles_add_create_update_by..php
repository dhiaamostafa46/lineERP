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
             $table->foreignId('created_by')->nullable()->constrained('users');
           $table->foreignId('updated_by')->nullable()->constrained('users');
             $table->foreignId('deleted_by')->nullable()->constrained('users');
           
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {


            // حذف الحقول الجديدة
            $table->dropColumn([
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        });
    }
};
