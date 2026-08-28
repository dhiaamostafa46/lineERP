<?php


function currency()
{
    $hr_setting = Modules\HR\App\Models\HrSetting::select('currency')->first();
    if ($hr_setting) {
        return $hr_setting->currency;
    }
    return 'SAR';
}
