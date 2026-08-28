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
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable()->unique();
        });
        
        // Auto-generate UUIDs for existing records
        $devices = \Illuminate\Support\Facades\DB::table('pos_devices')->whereNull('uuid')->get();
        foreach ($devices as $device) {
            \Illuminate\Support\Facades\DB::table('pos_devices')->where('id', $device->id)->update([
                'uuid' => \Illuminate\Support\Str::uuid()->toString()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
