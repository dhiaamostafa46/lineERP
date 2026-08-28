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
        Schema::dropIfExists('template_document_defaults');
        Schema::dropIfExists('template_custom_fields');
        Schema::dropIfExists('template_translations');
        Schema::dropIfExists('templates');

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->string('document_type')->nullable(); // sales_invoice, receipt, etc.
            $table->string('print_format')->default('A4'); // A4, thermal, compact
            $table->longText('header_html')->nullable();
            $table->longText('content_html')->nullable();
            $table->longText('footer_html')->nullable();
            $table->longText('css_styles')->nullable();
            $table->json('variables')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
