<?php

namespace Modules\HR\App\Livewire\Statistics\Holidays;

use Livewire\Component;
use Modules\HR\App\Models\HrHolidayType;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class CountTypes extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.holidays.count-types', $data);
    }

    public function getData()
    {
        // Fetching holiday types along with their holidays count
        $items = HrHolidayType::withCount('holidays')->get();

        // Create a new PieChartModel
        $chart = new PieChartModel();
        $chart->setTitle(__('hr::models/hr_holiday_types.plural'))    ->setAnimated(true)    ;
        $chart->asPie();

        // Adding slices to the chart
        foreach ($items as $index => $item) {
            // Ensure $item->name and $item->holidays_count are valid
            $chart->addSlice(
                $item->name,
                $item->holidays_count,
                config('colors')[$index] ?? '#000000', // Default color if index does not exist
                []
            );
        }

        return $chart;
    }
}
