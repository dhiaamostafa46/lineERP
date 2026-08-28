<?php

function formatMinutesToHours($minutes)
{
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;
    return sprintf('%02d:%02d', $hours, $remainingMinutes);
}

function secondsToTime($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $remainingSeconds = $seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
}

function secondsToMinutes($seconds)
{
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;

    return $minutes;
}

function   MinutesToSeconds($time)
{
    if (empty($time)) {
        return 0; // ارجع 0 إذا كانت القيمة فارغة
    }

    return $time *60 ;

}

function timeToSeconds($time)
{


    // التحقق مما إذا كانت القيمة فارغة
    if (empty($time)) {
        return 0; // ارجع 0 إذا كانت القيمة فارغة
    }


    // تقسيم الوقت بناءً على :
    $parts = explode(':', $time);

    // التأكد من وجود 3 أجزاء (ساعات، دقائق، ثواني)
    if (count($parts) === 3) {
        [$hours, $minutes, $seconds] = $parts;
        return $hours * 3600 + $minutes * 60 + $seconds;
    } elseif (count($parts) === 2) {
        // إذا كان الوقت يحتوي فقط على ساعات ودقائق بدون ثواني
        [$hours, $minutes] = $parts;
        return $hours * 3600 + $minutes * 60;
    }

    // في حال لم تكن الصيغة صحيحة، ارجع 0
    return 0;
}




//-----------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------
use Illuminate\Support\Facades\DB;

if (!function_exists('hr_setting')) {
    function hr_setting()
    {
        return DB::table('hr_settings')->first(); // بدون استخدام HrSetting::class
    }
}

if (!function_exists('setting')) {
    function setting()
    {
        return DB::table('settings')->first(); // بدون استخدام Setting::class
    }
}



if (!function_exists('accounting_settings')) {
    function accounting_settings()
    {
        return DB::table('accounting_settings')->first(); // نفس جدول الإعدادات أو جدول منفصل
    }
}

