<?php

namespace Modules\HR\App\Livewire\Statistics\Shifts;

use Livewire\Component;
use Modules\HR\App\Models\HrShiftType;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class CountEmployees extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.shifts.count-employees', $data);
    }

    public function getData()
    {
        $shifts = HrShiftType::select('id', 'deleted_at')->with('translations')->withCount('employees')->get();


        $chart = new PieChartModel();
        $chart->setTitle(__('hr::models/hr_employees.total_employees').'/'.__('hr::models/hr_shift_types.plural'))
        ->setType('pie') ->setAnimated(true)    ; // Ensure the chart type is donut

        // Loop through shifts and add data to the chart
        foreach ($shifts as $index => $shift) {
            // Add shift name, employee count, and a color from the config
            $chart->addSlice(
                $shift->name,  // Ensure that translations provide the correct name
                $shift->employees_count,
                config('colors')[$index] ?? '#000000' // Fallback color if not enough colors
            );
        }

        return $chart;
    }



}

