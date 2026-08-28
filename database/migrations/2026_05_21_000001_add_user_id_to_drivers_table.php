<?php

use App\Models\User;
use App\Models\Vehicles\Driver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->unique('user_id');
        });

        Driver::query()
            ->whereNull('user_id')
            ->each(function (Driver $driver): void {
                $phone = User::normalizeSaudiPhone((string) $driver->mobile);

                if ($phone === '') {
                    return;
                }

                $user = User::query()
                    ->where('user_type', 'driver')
                    ->where('phone', $phone)
                    ->first();

                if ($user !== null) {
                    $driver->update(['user_id' => $user->id]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
