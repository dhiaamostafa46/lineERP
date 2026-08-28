<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesPermissionsTableSeeder::class,
            UsersTableSeeder::class,
            SettingTableSeeder::class,
            TestDataSeeder::class,
            HrHolidayTypesSeeder::class,
            productsSeeder::class,
            AccountingSeeder::class,
            AccountMappingSeeder::class,
            WarehouseSeeder::class,
            InitialDataSeeder::class,
            EmployeeSeeder::class,
            AreaCitySeeder::class,
            ProjectSeeder::class,
            VehicleBrandModelSeeder::class,
            //   JournalEntrySeeder::class
            TemplateSeeder::class,
            //   JournalEntrySeeder::class
            PosDeviceSeeder::class,
         //   JournalEntrySeeder::class
            // LanguageLineSeeder::class,
        ]);
    }
}
