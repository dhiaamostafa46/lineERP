<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;

use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\BasicData\App\Http\Requests\CreateDbUnitRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbUnitRequest;
use Modules\BasicData\App\Repositories\DbUnitRepository;

use Maatwebsite\Excel\Facades\Excel;

class DbUnitController extends AppBaseController
{
    use \App\Traits\HasBulkActions;

    /** @var DbUnitRepository $dbUnitRepository*/
    private $dbUnitRepository;

    public function __construct(DbUnitRepository $dbUnitRepo)
    {
        $this->dbUnitRepository = $dbUnitRepo;
    }

    /**
     * Display a listing of the Unit.
     */
    public function index(Request $request)
    {
        $pagination = $request->get('pagination', 10);
        $data['units'] = $this->dbUnitRepository->allQuery($request->except('pagination'))->paginate($pagination);
        $data['statuses'] = $this->dbUnitRepository->statuses();

        $unitModel = \App\Models\BasicDataApp\Unit::class;
        $data['totalUnitsCount'] = $unitModel::count();
        $data['activeUnitsCount'] = $unitModel::where('status', 1)->count();

        return view('basicdata::units.index', $data);
    }

    /**
     * Show the form for creating a new Unit.
     */
    public function create()
    {
        $data['statuses'] = $this->dbUnitRepository->statuses();
        return view('basicdata::units.create', $data);
    }

    /**
     * Store a newly created Unit in storage.
     */
    public function store(CreateDbUnitRequest $request)
    {
        try {
            $input = $request->all();

            $unit = $this->dbUnitRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_units.singular')]));

            return redirect()->route('basicdata.units.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_units.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Unit.
     */
    public function show($id)
    {
        $unit = $this->dbUnitRepository->find($id);

        if (empty($unit)) {
            flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.units.index'));
        }

        return view('basicdata::units.show')->with('unit', $unit);
    }

    /**
     * Show the form for editing the specified Unit.
     */
    public function edit($id)
    {
        $unit = $this->dbUnitRepository->find($id);
        $data['statuses'] = $this->dbUnitRepository->statuses();
        $data['unit'] = $unit;

        if (empty($unit)) {
            flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.units.index'));
        }

        return view('basicdata::units.edit', $data);
    }

    /**
     * Update the specified Unit in storage.
     *
     * @param int $id
     * @param UpdateDbUnitRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDbUnitRequest $request, $id)
    {
        try {
            $unit = $this->dbUnitRepository->find($id);

            if (empty($unit)) {
                flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.units.index'));
            }

            $input = $request->all();
            $unit = $this->dbUnitRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_units.singular')]));

            return redirect()->route('basicdata.units.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_units.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Unit from storage.
     */
    public function destroy($id)
    {
        try {
            $unit = $this->dbUnitRepository->find($id);

            if (empty($unit)) {
                flash()->error(__('basicdata::models/db_units.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.units.index'));
            }

            $this->dbUnitRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_units.singular')]));

            return redirect()->route('basicdata.units.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_units.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // public function excel()
    // {

    //     $hidder  = $this->dbUnitRepository->header();
    //       $dataExel  = $this->dbUnitRepository->dataExel();
    //        $type  = "excel";

    //      return Excel::download(new BasicDataExport($dataExcel, $headers), 'unit.xlsx');
    // }

    //  public function csv()
    // {
    //      $hidder  = $this->dbUnitRepository->header();
    //       $dataExel  = $this->dbUnitRepository->dataExel();
    //        $type  = "excel";
    //     return Excel::download(new BasicDataExport($list), 'unit.csv');

    // }

    public function excel()
    {
        $headers = $this->dbUnitRepository->header();
        $dataExcel = $this->dbUnitRepository->dataExel(); // استخدم Unit بدلاً من dataExel

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'unit.xlsx');
    }

    public function csv()
    {
        $headers = $this->dbUnitRepository->header();
        $dataExcel = $this->dbUnitRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'unit.csv');
    }

    public function pdf()
    {
         $headers = $this->dbUnitRepository->header();
        $dataExcel = $this->dbUnitRepository->dataExel();
          $name = $this->dbUnitRepository->name();


            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->autoArabic = true;

            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;

            $mpdf->shrink_tables_to_fit = 1;
            $mpdf->keep_table_proportions = true;

            $mpdf->SetDisplayMode('fullpage');

            $mpdf->list_indent_first_level = 0;
            $mpdf->SetDirectionality(  app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
            $mpdf->WriteHTML(view('basicdata::exports.pdf', ['headers' => $headers ,'data'=>  $dataExcel ,'name'=> $name]));
            $mpdf->Output();


    }
}
