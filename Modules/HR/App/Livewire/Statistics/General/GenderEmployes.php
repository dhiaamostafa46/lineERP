<?php

namespace Modules\HR\App\Livewire\Statistics\General;

use Livewire\Component;
use Asantibanez\LivewireCharts\Models\PieChartModel; // Ensure to import PieChartModel
 use App\Models\Employee;// Import Employee model

class GenderEmployes extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData(); // Get chart data
        return view('hr::livewire.statistics.general.gender-employes', $data);
    }

    public function getData()
    {
        // Initialize a PieChartModel
        $chart = new PieChartModel();
       $chart->setTitle(__('hr::models/hr_employees.total_employees').'/'.__('models/employees.fields.gender'))    ->setAnimated(true)    ;
       $chart->asDonut();
        // Fetch the count of employees based on gender
        $maleCount = Employee::where('gender', Employee::GENDER_MALE)->count();
        $femaleCount = Employee::where('gender', Employee::GENDER_FEMALE)->count();

        // Add data to the chart
        $chart->addSlice(__('lang.male'), $maleCount, '#1f77b4'); // Example color for male
        $chart->addSlice(__('lang.female'), $femaleCount, '#ff7f0e'); // Example color for female

        // If you have a third gender category, you can add it like this
        // $otherCount = Employee::where('gender', Employee::NOT_DEFINED)->count();
        // $chart->addSlice(__('lang.not_defined'), $otherCount, '#2ca02c');

        return $chart; // Return the populated chart model
    }
}
