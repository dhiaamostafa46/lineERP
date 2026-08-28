<?php

namespace Modules\HR\App\Livewire\Statistics\Department;

use Livewire\Component;
use Modules\HR\App\Models\HrDepartment;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class CountEmployees extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.department.count-employees', $data);
    }

    public function getData()
    {
        // Fetch departments with employee counts
        $departments = HrDepartment::select('id', 'deleted_at')->with('translations')->withCount('employees')->get();

        // Create a new PieChartModel
        $pieChartModel = new PieChartModel();

        $pieChartModel->setTitle(__('hr::models/hr_employees.total_employees').'/'.__('hr::models/hr_departments.plural'));
        $pieChartModel->asPie(); // Set the chart type to pie

        // Adding slices to the chart
        foreach ($departments as $index => $department) {
            $pieChartModel->addSlice(
                $department->name,
                $department->employees_count,
                config('colors')[$index] ?? '#000000' // Use a default color if not defined
            );
        }

        return $pieChartModel; // Return the populated pie chart model
    }
}
