<?php

namespace Modules\HR\App\Livewire\Statistics\Assets;

use Livewire\Component;
use Modules\HR\App\Models\HrAssetType;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class CountTypes extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.assets.count-types', $data);
    }

    public function getData()
    {
        $items = HrAssetType::select('id', 'deleted_at')->withCount('assets')->get();
        $chart = new PieChartModel();
        $chart->setTitle(__('hr::models/hr_asset_types.plural'));
        $chart->asDonut();
        foreach ($items as $index => $item) {
            $chart->addSlice($item->name, $item->assets_count, config('colors')[$index], []);
        }
        return $chart;
    }
}
