<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hr_posts')) {
            Schema::create('hr_posts', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('type')->default(1)->comment('1=news, 2=announcement');
                $table->unsignedTinyInteger('status')->default(1)->comment('1=draft, 2=published');
                $table->unsignedTinyInteger('flage')->default(1)->comment('1=all, 2=employees, 3=department, 4=branches');
                $table->json('employee_id')->nullable();
                $table->json('department_id')->nullable();
                $table->json('branch_id')->nullable();
                $table->dateTime('published_at')->nullable()->index();
                $table->dateTime('expires_at')->nullable()->index();
                $table->boolean('is_pinned')->default(false);
                $table->string('image_path')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('hr_post_translations')) {
            Schema::create('hr_post_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_post_id');
                $table->string('locale', 10)->index();
                $table->string('title', 255);
                $table->longText('body')->nullable();
                $table->unique(['hr_post_id', 'locale'], 'hr_post_locale_unique');
                $table->foreign('hr_post_id', 'fk_hr_post_trans')->references('id')->on('hr_posts')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_post_translations');
        Schema::dropIfExists('hr_posts');
    }
};
