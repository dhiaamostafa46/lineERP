<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use App\Livewire\TemplateBuilder;
use ReflectionClass;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing templates to ensure ONLY two basic templates exist
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('template_translations')->truncate();
        \DB::table('templates')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $builder = new TemplateBuilder();
        $builder->mount();

        $reflection = new ReflectionClass($builder);
        $method = $reflection->getMethod('generateHtmls');
        $method->setAccessible(true);

        // 1. A4 Default Template
        $builder->print_format = 'A4';
        $builder->document_type = 'SalesInvoice';
        $htmlsA4 = $method->invoke($builder);

        $dataA4 = [
            'document_type' => 'SalesInvoice',
            'print_format' => 'A4',
            'is_default' => 1,
            'variables' => $builder->templateConfig,
            'status' => 1,
        ];
        $dataA4 = array_merge($dataA4, $htmlsA4);

        $templateA4 = Template::whereHas('translations', function ($q) {
            $q->where('name', 'A4');
        })->first() ?? new Template();

        $templateA4->fill($dataA4);
        $templateA4->save();
        $templateA4->translateOrNew('ar')->name = 'تصميم فواتير  A4';
        $templateA4->translateOrNew('en')->name = 'A4';
        $templateA4->save();

        // 2. THERMAL Default Template
        $builder->print_format = 'thermal';
        $builder->document_type = 'SalesInvoice';
        $htmlsThermal = $method->invoke($builder);

        $dataThermal = [
            'document_type' => 'SalesInvoice',
            'print_format' => 'thermal',
            'is_default' => 1,
            'variables' => $builder->templateConfig,
            'status' => 1,
        ];
        $dataThermal = array_merge($dataThermal, $htmlsThermal);

        $templateThermal = Template::whereHas('translations', function ($q) {
            $q->where('name', 'THERMAL');
        })->first() ?? new Template();

        $templateThermal->fill($dataThermal);
        $templateThermal->save();
        $templateThermal->translateOrNew('ar')->name = 'تصميم فواتير حراري';
        $templateThermal->translateOrNew('en')->name = 'THERMAL';
        $templateThermal->save();

        if (isset($this->command)) {
            $this->command->info('Default templates (A4 and THERMAL) seeded successfully using TemplateBuilder logic.');
        }
    }
}
