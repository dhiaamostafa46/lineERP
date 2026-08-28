<?php

namespace Modules\HR\App\Livewire\Statistics\Jobs;

use Livewire\Component;
use Modules\HR\App\Models\HrJob;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class CountEmployees extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.jobs.count-employees', $data);
    }

    public function getData()
    {
        $jobs = HrJob::select('id', 'deleted_at')->with('translations')->withCount('employees')->get();
        $chart = new PieChartModel();
        $chart->setTitle(__('hr::models/hr_jobs.plural')) ->setAnimated(true)    ;
        $chart->asDonut();
        foreach ($jobs as $index => $job) {
            $chart->addSlice($job->name, $job->employees_count, config('colors')[$index], []);
        }
        return $chart;
    }
}
