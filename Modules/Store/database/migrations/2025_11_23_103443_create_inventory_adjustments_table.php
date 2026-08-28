<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //

        // Schema::create('inventory_adjustments', function (Blueprint $table) {
        //     $table->id();


        //     $table->unsignedBigInteger('org_id');
        //     $table->unsignedBigInteger('branch_id');
        //     $table->foreignId('user_id')->nullable()->constrained();
        //     $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
        //     $table->string('adjustment_number')->unique();
        //     $table->date('adjustment_date');

        //     $table->unsignedTinyInteger('type')->default(1)->comment('0=opening_balance,1=adjustment,2=damage');
        //     $table->unsignedTinyInteger('sub_type')->default(2)->comment('0=increase,1=decrease,2=correction');
        //     $table->unsignedTinyInteger('reason')->default(5)->comment('0=count_variance,1=damage,2=theft,3=expired,4=opening_balance,5=other');
        //     $table->unsignedTinyInteger('status')->default(0)->comment('0=draft,1=approved,2=processed,3=cancelled');

        //     $table->decimal('total_quantity', 15, 4)->default(0);
        //     $table->decimal('total_value', 15, 4)->default(0);

        //     $table->foreignId('created_by')->constrained('users');
        //     $table->foreignId('approved_by')->nullable()->constrained('users');
        //     $table->timestamp('approved_at')->nullable();

        //     $table->text('notes')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // Schema::create('inventory_adjustment_items', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('org_id');
        //     $table->unsignedBigInteger('branch_id');
        //     $table->foreignId('user_id')->nullable()->constrained();
        //     $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
        //     $table->foreignId('inventory_adjustment_id')->constrained('inventory_adjustments')->onDelete('cascade');
        //     $table->foreignId('product_id')->constrained('products');
        //     $table->foreignId('unit_id')->constrained('units');
        //     $table->decimal('quantity', 15, 4);
        //     $table->decimal('unit_cost', 15, 4);
        //     $table->decimal('total_cost', 15, 4);
        //     // للحالات التي تحتاج إلى مقارنة بالرصيد النظامي (مثل التعديلات)
        //     $table->decimal('system_quantity', 15, 4)->nullable()->comment('الكمية في النظام قبل التعديل');
        //     $table->decimal('variance', 15, 4)->nullable()->comment('الفرق بين الفعلي والنظامي');

        //     $table->text('notes')->nullable();
        //     $table->unsignedBigInteger('movement_id')->nullable()->comment('الحركة المولدة');

        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('inventory_adjustment_items');
        // Schema::dropIfExists('inventory_adjustments');
    }
};
