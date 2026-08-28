<?php

namespace Modules\HR\App\Livewire\Statistics\General;

use App\Models\Employee;
use Livewire\Component;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrEmployee;

class NationalityEmployes extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.general.nationality-employes', $data);
    }

    public function getData()
    {
        // Initialize a ColumnChartModel
        $chart = new ColumnChartModel();
        $chart->setTitle(__('hr::lang.nationality_distribution')) // You can localize this title
              ->setAnimated(true);

        // Fetch nationality counts
        $nationalities = Employee::select('nationality', DB::raw('count(*) as total'))
            ->groupBy('nationality')
            ->get();

        // Adding columns to the chart
        foreach ($nationalities as $nationality) {
            $chart->addColumn(
                $nationality->nationality ?? __('hr::lang.not_specified'),
                $nationality->total,
                $this->rand_color()
            );
        }

        return $chart; // Return the populated chart model
    }

    private function rand_color()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
