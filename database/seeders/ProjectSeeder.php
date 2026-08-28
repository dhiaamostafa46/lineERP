<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->where('code', 'DEMO')->first();

        if ($company === null) {
            $company = new Company;
            $company->code = 'DEMO';
            $company->status = Company::STATUS_ACTIVE;
            $company->save();
            $company->translateOrNew('en')->name = 'Demo Company';
            $company->translateOrNew('ar')->name = 'شركة تجريبية';
            $company->save();
        }

        $projects = [
            [
                'code' => 'PRJ-001',
                'status' => Project::STATUS_ACTIVE,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'en' => 'Main Project',
                'ar' => 'المشروع الرئيسي',
            ],
            [
                'code' => 'PRJ-002',
                'status' => Project::STATUS_ACTIVE,
                'start_date' => '2026-03-01',
                'end_date' => '2026-09-30',
                'en' => 'Expansion Project',
                'ar' => 'مشروع التوسع',
            ],
            [
                'code' => 'PRJ-003',
                'status' => Project::STATUS_INACTIVE,
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'en' => 'Completed Pilot',
                'ar' => 'المشروع التجريبي المكتمل',
            ],
        ];

        foreach ($projects as $item) {
            $project = Project::query()->updateOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => $item['code'],
                ],
                [
                    'status' => $item['status'],
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'],
                ]
            );

            $project->translateOrNew('en')->name = $item['en'];
            $project->translateOrNew('ar')->name = $item['ar'];
            $project->save();
        }
    }
}
