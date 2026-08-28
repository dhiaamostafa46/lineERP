<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('mapping_key')->index();
            $table->foreignId('account_id')->constrained('tree_accounts')->cascadeOnDelete();
            $table->nullableMorphs('entity');
            $table->unsignedTinyInteger('status')->default(2)->comment('1=inactive, 2=active');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // منع تكرار التوجيه
            $table->unique(['mapping_key', 'entity_type', 'entity_id'], 'unique_account_mapping');
        });

        /*
        |--------------------------------------------------------------------------
        | Translations Table
        |--------------------------------------------------------------------------
        */
        Schema::create('account_mapping_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_mapping_id')->constrained('account_mappings')->cascadeOnDelete();

            $table->string('locale', 5)->index();
            $table->string('name');

            $table->unique(['account_mapping_id', 'locale'], 'unique_account_mapping_translation');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_mapping_translations');
        Schema::dropIfExists('account_mappings');
    }
};
