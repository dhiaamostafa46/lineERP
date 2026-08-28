<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {



        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id(); // Primary key

                $table->string('CR')->nullable(); // السجل التجاري (CR)
                $table->string('logo')->nullable(); // الشعار
                $table->string('signature')->nullable(); // التوقيع (signature)
                $table->string('status')->nullable()->default(1); // الحالة
                $table->string('activity')->nullable(); // النشاط
                $table->boolean('is_new')->default(1); // جديد (isNew)
                $table->boolean('is_paid')->default(1); // مدفوع (isPaid)
                $table->string('pay_gate_status')->nullable(); // حالة بوابة الدفع
                $table->string('insurance_sub_no')->nullable(); // رقم إشتراك بالتأمين (insurance_subscription_number)
                $table->string('chamber_no')->nullable(); // رقم الغرفة التجارية (chamber_number)
                $table->string('organization_number')->nullable(); // رقم المنشأة (organization_number)
                $table->string('national_address')->nullable(); // العنوان الوطني (national_address)
                $table->string('tax_number')->nullable();
                $table->string('seal')->nullable();  // الرقم الضريبي (tax_number)
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('organization_translations')) {
            Schema::create('organization_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade'); // Foreign key to 'branches'
                $table->string('name')->nullable();
                $table->string('address')->nullable();
                $table->string('locale')->index(); // Locale
                $table->unique(['organization_id', 'locale']);
                $table->timestamps(); // Created and Updated timestamps
            });
        }

        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained();
                $table->unsignedBigInteger('org_id')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('area', 255)->nullable();
                $table->string('city', 255)->nullable();
                $table->string('district', 255)->nullable();
                $table->string('long', 255)->nullable();
                $table->string('lat', 255)->nullable();
                $table->double('distance')->default(0);
                $table->string('manager')->default(0);
                $table->text('description')->nullable();
                $table->boolean('is_main')->default(false);
                $table->unsignedTinyInteger('status')->default(1);
                $table->timestamps(); // Created and Updated timestamps
                $table->softDeletes(); // Soft deletes
            });
        }

        if (!Schema::hasTable('branch_translations')) {
            Schema::create('branch_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); // Foreign key to 'branches'
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('locale')->index(); // Locale
                $table->unique(['branch_id', 'locale']);
                $table->timestamps(); // Created and Updated timestamps
            });
        }

        if (!Schema::hasTable('stores')) {
            Schema::create('stores', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('org_id')->nullable();
                $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
                $table->string('type')->default('main'); // main, secondary, quarantine
                $table->string('location')->nullable();
                $table->boolean('status')->default(true);
                $table->foreignId('tree_account_id')->nullable()->constrained('tree_accounts')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('store_translations')) {
            Schema::create('store_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained('stores')->onDelete('cascade'); // Foreign key to 'stores'
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('locale')->index(); // Locale
                $table->unique(['store_id', 'locale']);
                $table->timestamps(); // Created and Updated timestamps
            });
        }
    }
//2020_01
    public function down(): void
    {
        Schema::dropIfExists('store_translations');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('branch_translations');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('organization_translations');
        Schema::dropIfExists('organizations');
    }
};
