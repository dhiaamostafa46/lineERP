
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
        Schema::create('asset_impairments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->date('assessment_date');
            $table->decimal('recoverable_amount', 15, 2)->comment('المبلغ القابل للاسترداد');
            $table->decimal('book_value_at_assessment', 15, 2)->comment('القيمة الدفترية وقت التقييم');
            $table->decimal('impairment_loss', 15, 2)->default(0)->comment('خسارة الانخفاض');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->text('basis')->nullable()->comment('أساس التقييم');
            $table->boolean('is_reversed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_impairments');
    }
};
