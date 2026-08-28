<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to clean hidden Unicode characters from phone numbers.
     */
    public function up(): void
    {
        // Clean phone numbers in users table
        $users = DB::table('users')->select('id', 'phone')->get();
        foreach ($users as $user) {
            if ($user->phone) {
                $clean = preg_replace('/[^\d\+]/', '', $user->phone);
                if ($clean !== $user->phone) {
                    DB::table('users')->where('id', $user->id)->update(['phone' => $clean]);
                }
            }
        }

        // Clean phone numbers in employees table
        $employees = DB::table('employees')->select('id', 'phone')->get();
        foreach ($employees as $employee) {
            if ($employee->phone) {
                $clean = preg_replace('/[^\d\+]/', '', $employee->phone);
                if ($clean !== $employee->phone) {
                    DB::table('employees')->where('id', $employee->id)->update(['phone' => $clean]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
