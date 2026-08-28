<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\App\Models\HrHolidayType;

class HrHolidayTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            [
                'en'       => ['name' => 'Annual'],
                'ar'       => ['name' => 'إجازة سنوية'],
                'off_days' => 21,
                'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Sick'],
                'ar'       => ['name' => 'إجازة مرضية'],
                'off_days' => 120,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Emergency'],
                'ar'       => ['name' => 'إجازة اضطرارية'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Unpaid'],
                'ar'       => ['name' => 'إجازة بدون راتب'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Public Holiday / Eid'],
                'ar'       => ['name' => 'إجازة عيد / إجازات رسمية'],
                'off_days' => 10,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Marriage'],
                'ar'       => ['name' => 'إجازة زواج'],
                'off_days' => 5,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Bereavement'],
                'ar'       => ['name' => 'إجازة وفاة'],
                'off_days' => 5,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Maternity'],
                'ar'       => ['name' => 'إجازة أمومة'],
                'off_days' => 84,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Paternity'],
                'ar'       => ['name' => 'إجازة أبوة'],
                'off_days' => 3,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Hajj'],
                'ar'       => ['name' => 'إجازة حج'],
                'off_days' => 15,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Compensatory'],
                'ar'       => ['name' => 'إجازة تعويضية'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Patient Escort'],
                'ar'       => ['name' => 'إجازة مرافقة مريض'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Study'],
                'ar'       => ['name' => 'إجازة دراسية'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Delivery'],
                'ar'       => ['name' => 'إجازة وضع'],
                'off_days' => 84,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Examination'],
                'ar'       => ['name' => 'إجازة اختبار'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Exceptional'],
                'ar'       => ['name' => 'إجازة استثنائية'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
            [
                'en'       => ['name' => 'Official Business'],
                'ar'       => ['name' => 'إجازة عمل رسمي / مهمة عمل'],
                'off_days' => 0,
                'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
                'status'   => HrHolidayType::STATUS_ACTIVE,
            ],
        ];

        foreach ($holidays as $holiday) {
            $exists = HrHolidayType::whereTranslation('name', $holiday['en']['name'], 'en')->exists();
            if (!$exists) {
                HrHolidayType::create($holiday);
            }
        }
    }
}
