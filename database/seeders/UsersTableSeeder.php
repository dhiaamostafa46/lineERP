<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        // 1. Create Default User
        if (!DB::table('users')->where('email', 'user@email.com')->exists()) {
            DB::table('users')->insert([
                'id'         => 1,
                'name'       => 'user',
                'email'      => 'user@email.com',
                'phone'      => '0500100200',
                'org_id'     => 1,
                'branch_id'  => 1,
                'password'   => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Add Device Session for User 1 (Based on the provided data)
        if (!DB::table('device_sessions')->where('id', 1)->exists()) {
            DB::table('device_sessions')->insert([
                'id'               => 1,
                'user_id'          => 1,
                'org_id'           => null,
                'device_token'     => null,
                'device_serial'    => '35f5ff4f09c4c76614c216644ea7aa07985b8474d4fa99d0589f9c92ed0ba894',
                'device_name'      => null,
                'user_agent'       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',
                'device_ip'        => null,
                'ip'               => null,
                'device_type'      => 'Desktop',
                'browser'          => 'Chrome',
                'os'               => 'Windows',
                'is_active'        => 1,
                'last_activity_at' => '2026-04-15 09:48:16',
                'created_at'       => '2026-04-15 09:48:16',
                'updated_at'       => '2026-04-15 09:48:16',
            ]);
        }

     
    }
}
