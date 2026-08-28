<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrSalaryRequest;
use Modules\HR\App\Http\Requests\UpdateHrSalaryRequest;
use App\Http\Controllers\AppBaseController;

use Modules\HR\App\Repositories\HrSalaryRepository;

use Modules\HR\App\Imports\SalaryImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HrSalaryController extends AppBaseController
{
    /** @var HrSalaryRepository $hrSalaryRepository*/
    private $hrSalaryRepository;

    public function __construct(HrSalaryRepository $hrSalaryRepo)
    {
        $this->hrSalaryRepository = $hrSalaryRepo;
    }

    /**
     * Display a listing of the HrSalary.
     */
    public function index(Request $request)
    {
        $data['salaries'] = $this->hrSalaryRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 10);
        $data['employees'] = $this->hrSalaryRepository->filter_employees();

        return view('hr::salaries.index', $data);
    }

    /**
     * Show the form for creating a new HrSalary.
     */
    public function create()
    {
        $data['employees'] = $this->hrSalaryRepository->employees();
        $data['allowances'] = $this->hrSalaryRepository->allowances();
        $data['deducts'] = $this->hrSalaryRepository->deducts();
        return view('hr::salaries.create', $data);
    }

    /**
     * Store a newly created HrSalary in storage.
     */
    public function store(CreateHrSalaryRequest $request)
    {
        $input = $request->all();

        $salary = $this->hrSalaryRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_salaries.singular')]));

        return redirect(route('hr.salaries.index'));
    }

    /**
     * Display the specified HrSalary.
     */
    public function show($id)
    {
        $data['salary'] = $this->hrSalaryRepository->find($id);

        if (empty($data['salary'])) {
            flash()->error(__('hr::models/hr_salaries.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.salaries.index'));
        }

        return view('hr::salaries.show', $data);
    }

    /**
     * Show the form for editing the specified HrSalary.
     */
    public function edit($id)
    {
        $salary = $this->hrSalaryRepository->find($id);

        if (empty($salary)) {
            flash()->error(__('hr::models/hr_salaries.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.salaries.index'));
        }
        $data['allowances'] = $this->hrSalaryRepository->allowances();
        $data['deducts'] = $this->hrSalaryRepository->deducts();
        $data['salary'] = $salary->load('allowances', 'deducts');
        $data['salary_allowances'] = $salary->allowances->pluck('pivot.amount', 'id')->toArray();
        $data['salary_deducts'] = $salary->deducts->pluck('pivot.amount', 'id')->toArray();
        return view('hr::salaries.edit', $data);
    }

    /**
     * Update the specified HrSalary in storage.
     */
    public function update($id, UpdateHrSalaryRequest $request)
    {
        $salary = $this->hrSalaryRepository->find($id);

        if (empty($salary)) {
            flash()->error(__('hr::models/hr_salaries.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.salaries.index'));
        }
        $salary = $this->hrSalaryRepository->update($request->all(), $id);
        $this->hrSalaryRepository->create_allowances($request->allowances, $id);
        $this->hrSalaryRepository->create_deducts($request->deducts, $id);
        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_salaries.singular')]));

        return redirect(route('hr.salaries.index'));
    }

    /**
     * Remove the specified HrSalary from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $salary = $this->hrSalaryRepository->find($id);

        if (empty($salary)) {
            flash()->error(__('hr::models/hr_salaries.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.salaries.index'));
        }

        $this->hrSalaryRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_salaries.singular')]));

        return redirect(route('hr.salaries.index'));
    }


      /**
     * import salaries from excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importSalaries(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new SalaryImport, $request->file('file'));
        flash()->success(__('messages.imported', ['model' => __('hr::models/hr_salaries.singular')]));
        return redirect()->back();
    }
}
