<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

class AreaCitySeeder extends Seeder
{
    public function run(): void
    {
        $areas = [

            'riyadh' => [
                'ar' => 'منطقة الرياض',
                'en' => 'Riyadh Region',
                'cities' => [
                    ['code' => 'RUH', 'ar' => 'الرياض', 'en' => 'Riyadh'],
                    ['code' => 'alkharj', 'ar' => 'الخرج', 'en' => 'Al Kharj'],
                    ['code' => 'almajmaah', 'ar' => 'المجمعة', 'en' => 'Al Majmaah'],
                    ['code' => 'aldawadmi', 'ar' => 'الدوادمي', 'en' => 'Al Dawadmi'],
                    ['code' => 'wadi_aldawasir', 'ar' => 'وادي الدواسر', 'en' => 'Wadi Al Dawasir'],
                ],
            ],

            'makkah' => [
                'ar' => 'منطقة مكة المكرمة',
                'en' => 'Makkah Region',
                'cities' => [
                    ['code' => 'makkah', 'ar' => 'مكة المكرمة', 'en' => 'Makkah'],
                    ['code' => 'JED', 'ar' => 'جدة', 'en' => 'Jeddah'],
                    ['code' => 'taif', 'ar' => 'الطائف', 'en' => 'Taif'],
                    ['code' => 'rabigh', 'ar' => 'رابغ', 'en' => 'Rabigh'],
                    ['code' => 'qunfudhah', 'ar' => 'القنفذة', 'en' => 'Al Qunfudhah'],
                ],
            ],

            'madinah' => [
                'ar' => 'منطقة المدينة المنورة',
                'en' => 'Madinah Region',
                'cities' => [
                    ['code' => 'madinah', 'ar' => 'المدينة المنورة', 'en' => 'Madinah'],
                    ['code' => 'yanbu', 'ar' => 'ينبع', 'en' => 'Yanbu'],
                    ['code' => 'ula', 'ar' => 'العلا', 'en' => 'Al Ula'],
                    ['code' => 'badr', 'ar' => 'بدر', 'en' => 'Badr'],
                ],
            ],

            'eastern' => [
                'ar' => 'المنطقة الشرقية',
                'en' => 'Eastern Province',
                'cities' => [
                    ['code' => 'dammam', 'ar' => 'الدمام', 'en' => 'Dammam'],
                    ['code' => 'khobar', 'ar' => 'الخبر', 'en' => 'Khobar'],
                    ['code' => 'dhahran', 'ar' => 'الظهران', 'en' => 'Dhahran'],
                    ['code' => 'jubail', 'ar' => 'الجبيل', 'en' => 'Jubail'],
                    ['code' => 'ahsa', 'ar' => 'الأحساء', 'en' => 'Al Ahsa'],
                    ['code' => 'qatif', 'ar' => 'القطيف', 'en' => 'Qatif'],
                    ['code' => 'hafr_albatin', 'ar' => 'حفر الباطن', 'en' => 'Hafr Al Batin'],
                ],
            ],

            'qassim' => [
                'ar' => 'منطقة القصيم',
                'en' => 'Qassim Region',
                'cities' => [
                    ['code' => 'buraidah', 'ar' => 'بريدة', 'en' => 'Buraidah'],
                    ['code' => 'unaizah', 'ar' => 'عنيزة', 'en' => 'Unaizah'],
                    ['code' => 'rass', 'ar' => 'الرس', 'en' => 'Ar Rass'],
                ],
            ],

            'asir' => [
                'ar' => 'منطقة عسير',
                'en' => 'Asir Region',
                'cities' => [
                    ['code' => 'abha', 'ar' => 'أبها', 'en' => 'Abha'],
                    ['code' => 'khamis_mushait', 'ar' => 'خميس مشيط', 'en' => 'Khamis Mushait'],
                    ['code' => 'bisha', 'ar' => 'بيشة', 'en' => 'Bisha'],
                ],
            ],

            'tabuk' => [
                'ar' => 'منطقة تبوك',
                'en' => 'Tabuk Region',
                'cities' => [
                    ['code' => 'tabuk', 'ar' => 'تبوك', 'en' => 'Tabuk'],
                    ['code' => 'duba', 'ar' => 'ضباء', 'en' => 'Duba'],
                    ['code' => 'umluj', 'ar' => 'أملج', 'en' => 'Umluj'],
                ],
            ],

            'hail' => [
                'ar' => 'منطقة حائل',
                'en' => 'Hail Region',
                'cities' => [
                    ['code' => 'hail', 'ar' => 'حائل', 'en' => 'Hail'],
                ],
            ],

            'jazan' => [
                'ar' => 'منطقة جازان',
                'en' => 'Jazan Region',
                'cities' => [
                    ['code' => 'jazan', 'ar' => 'جازان', 'en' => 'Jazan'],
                    ['code' => 'sabya', 'ar' => 'صبيا', 'en' => 'Sabya'],
                    ['code' => 'abuarish', 'ar' => 'أبو عريش', 'en' => 'Abu Arish'],
                ],
            ],

            'najran' => [
                'ar' => 'منطقة نجران',
                'en' => 'Najran Region',
                'cities' => [
                    ['code' => 'najran', 'ar' => 'نجران', 'en' => 'Najran'],
                    ['code' => 'sharurah', 'ar' => 'شرورة', 'en' => 'Sharurah'],
                ],
            ],

            'bahah' => [
                'ar' => 'منطقة الباحة',
                'en' => 'Al Bahah Region',
                'cities' => [
                    ['code' => 'bahah', 'ar' => 'الباحة', 'en' => 'Al Bahah'],
                    ['code' => 'baljurashi', 'ar' => 'بلجرشي', 'en' => 'Baljurashi'],
                ],
            ],

            'jouf' => [
                'ar' => 'منطقة الجوف',
                'en' => 'Al Jouf Region',
                'cities' => [
                    ['code' => 'sakaka', 'ar' => 'سكاكا', 'en' => 'Sakaka'],
                    ['code' => 'qurayyat', 'ar' => 'القريات', 'en' => 'Qurayyat'],
                ],
            ],

            'northern_borders' => [
                'ar' => 'منطقة الحدود الشمالية',
                'en' => 'Northern Borders Region',
                'cities' => [
                    ['code' => 'arar', 'ar' => 'عرعر', 'en' => 'Arar'],
                    ['code' => 'rafha', 'ar' => 'رفحاء', 'en' => 'Rafha'],
                    ['code' => 'turaif', 'ar' => 'طريف', 'en' => 'Turaif'],
                ],
            ],
        ];

        foreach ($areas as $areaCode => $areaData) {

            $area = Area::create([
                'code' => $areaCode,
                'status' => 2,
            ]);

            $area->translations()->createMany([
                [
                    'locale' => 'ar',
                    'name' => $areaData['ar'],
                ],
                [
                    'locale' => 'en',
                    'name' => $areaData['en'],
                ],
            ]);

            foreach ($areaData['cities'] as $cityData) {

                $city = City::create([
                    'area_id' => $area->id,
                    'code' => $cityData['code'],
                    'status' => 2,
                ]);

                $city->translations()->createMany([
                    [
                        'locale' => 'ar',
                        'name' => $cityData['ar'],
                    ],
                    [
                        'locale' => 'en',
                        'name' => $cityData['en'],
                    ],
                ]);
            }
        }
    }
}
