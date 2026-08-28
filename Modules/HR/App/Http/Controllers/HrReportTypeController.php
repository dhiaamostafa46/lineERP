<?php

namespace Modules\HR\App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CustomExport;
use App\Exports\HrEmployeesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrReportTypeRepository;
use Modules\HR\App\Http\Requests\CreateHrReportTypeRequest;
use Modules\HR\App\Http\Requests\UpdateHrReportTypeRequest;


class HrReportTypeController extends AppBaseController
{
    /** @var HrReportTypeRepository $hrReportTypeRepository*/
    private $hrReportTypeRepository;

    public function __construct(HrReportTypeRepository $hrReportTypeRepo)
    {
        $this->hrReportTypeRepository = $hrReportTypeRepo;
    }

    /**
     * Display a listing of the HrReportType.
     */
    public function index(Request $request)
    {
        $data['report_types'] = $this->hrReportTypeRepository->paginate(10);
        $data['statuses'] = $this->hrReportTypeRepository->statuses();

        return view('hr::report_types.index', $data);
    }

    /**
     * Show the form for creating a new HrReportType.
     */
    public function create()
    {
        $data['statuses'] = $this->hrReportTypeRepository->statuses();

        return view('hr::report_types.create', $data);
    }

    /**
     * Store a newly created HrReportType in storage.
     */
    public function store(CreateHrReportTypeRequest $request)
    {
        $input = $request->all();

        $report_type = $this->hrReportTypeRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_report_types.singular')]));

        return redirect(route('hr.report_types.index'));
    }

    /**
     * Display the specified HrReportType.
     */
    public function show($id)
    {
        $report_type = $this->hrReportTypeRepository->find($id);

        if (empty($report_type)) {
            flash()->error(__('hr::models/hr_report_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.report_types.index'));
        }

        return view('hr::report_types.show')->with('hrReportType', $report_type);
    }

    /**
     * Show the form for editing the specified HrReportType.
     */
    public function edit($id)
    {
        $data['report_type'] = $this->hrReportTypeRepository->find($id);

        if (empty($data['report_type'])) {
            flash()->error(__('hr::models/hr_report_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.report_types.index'));
        }
        $data['statuses'] = $this->hrReportTypeRepository->statuses();


        return view('hr::report_types.edit', $data);
    }

    /**
     * Update the specified HrReportType in storage.
     */
    public function update($id, UpdateHrReportTypeRequest $request)
    {
        $report_type = $this->hrReportTypeRepository->find($id);

        if (empty($report_type)) {
            flash()->error(__('hr::models/hr_report_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.report_types.index'));
        }

        $report_type = $this->hrReportTypeRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_report_types.singular')]));

        return redirect(route('hr.report_types.index'));
    }

    /**
     * Remove the specified HrReportType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $report_type = $this->hrReportTypeRepository->find($id);

        if (empty($report_type)) {
            flash()->error(__('hr::models/hr_report_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.report_types.index'));
        }

        $this->hrReportTypeRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_report_types.singular')]));

        return redirect(route('hr.report_types.index'));
    }

    /**
     * Restore the specified HrReportType from storage.
     *
     * @throws \Exception
     */
    public function export($id)
    {
        $report_type = $this->hrReportTypeRepository->find($id);

        if (empty($report_type)) {
            flash()->error(__('hr::models/hr_report_types.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.report_types.index'));
        }
        $data = [];

        switch ($id) {
            case '1':
                $data = $this->getEmployeeSalaries();
                break;
            case '2':
                // code
                break;
            default:
                flash()->error(__('hr::models/hr_report_types.singular') . ' ' . __('messages.not_found'));
                return redirect(route('hr.report_types.index'));
        }
        return Excel::download(new CustomExport($data['items'], $data['header'], $data['style']), $report_type->name . '.xlsx');
    }

    private function getEmployeeSalaries()
    {
        $data['items'] = [];
        $employees = \Modules\HR\App\Models\HrEmployee::get();
        foreach ($employees as $employee) {
            $data['items'][] = [
                $employee->username,
                $employee->department->name,
                $employee->job->name,
                $employee->job_level,
                $employee->salary->basic . ' ' . currency(),
            ];
        }
        $data['header'] = [
            'Username',
            'Department',
            'Job',
            'Job Level',
            'Basic',
        ];
        $data['style']  = [
            1    => ['font' => ['bold' => true]],
            'A'    => ['font' => ['bold' => true]],
        ];

        return $data;
    }
}
