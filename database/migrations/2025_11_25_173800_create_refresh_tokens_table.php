<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
                Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 128)->unique(); // store hashed value if you prefer
            $table->boolean('revoked')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['user_id', 'revoked', 'expires_at']);
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }

};
