<?php

namespace Modules\HR\App\Livewire\Statistics\Payroll;

use Livewire\Component;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Carbon\Carbon;
use Modules\HR\App\Models\HrPayroll;

class Payrolls extends Component
{
    public function render()
    {
        $data['data_chart'] = $this->getData();
        return view('hr::livewire.statistics.payroll.payrolls', $data);
    }

    public function getData()
    {
        $year = Carbon::now()->year; // Get current year

        // Initialize an array for months with default values of 0
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = 0; // Set default value to 0 for each month
        }

        // Fetch payroll data for the current year
        $payrollData = HrPayroll::selectRaw('SUM(total) as total, MONTH(payroll_date) as month')
            ->whereYear('payroll_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Populate the months array with actual payroll data
        foreach ($payrollData as $data) {
            $months[$data->month] = $data->total; // Update month value with total
        }

        $chart = new LineChartModel();
        $chart->setTitle(__('hr::models/hr_payrolls.plural')) ->setAnimated(true)    ;// Set the chart title

        // Prepare data for the chart
        foreach ($months as $month => $total) {
            $monthName = Carbon::create()->month($month)->translatedFormat('F'); // Get the month name
            $chart->addPoint($monthName, $total); // Add month and total to chart
        }

        return $chart;
    }

}
