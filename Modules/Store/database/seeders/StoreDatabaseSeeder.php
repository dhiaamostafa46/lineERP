<?php

namespace Modules\Store\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Store\database\seeders\SettingsTableSeeder;

class StoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $this->call([
            SettingsTableSeeder::class,
            // LanguageLineSeeder::class,
        ]);
    }
}
