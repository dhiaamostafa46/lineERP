<?php

namespace Modules\HR\database\seeders;

use Illuminate\Database\Seeder;

class HrDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsTableSeeder::class,
        ]);
    }
}
