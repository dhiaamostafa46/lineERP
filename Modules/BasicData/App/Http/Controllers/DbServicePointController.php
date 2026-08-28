<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\BasicData\App\Http\Requests\CreateDbServicePointRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbServicePointRequest;
use Modules\BasicData\App\Repositories\DbServicePointRepository;

class DbServicePointController extends AppBaseController
{
    /** @var DbServicePointRepository $dbServicePointRepository*/
    private $dbServicePointRepository;

    public function __construct(DbServicePointRepository $dbServicePointRepo)
    {
        $this->dbServicePointRepository = $dbServicePointRepo;
    }

    /**
     * Display a listing of the ServicePoint.
     */
    public function index(Request $request)
    {
        $data['servicePoints'] = $this->dbServicePointRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['statuses'] = $this->dbServicePointRepository->statuses();
        $data['types'] = $this->dbServicePointRepository->types();
        return view('basicdata::service_points.index', $data);
    }

    /**
     * Show the form for creating a new ServicePoint.
     */
    public function create()
    {
           $data['statuses'] = $this->dbServicePointRepository->statuses();
           $data['types'] = $this->dbServicePointRepository->types();
        return view('basicdata::service_points.create' , $data);
    }

    /**
     * Store a newly created ServicePoint in storage.
     */
    public function store(CreateDbServicePointRequest $request)
    {
        try {
            $input = $request->all();

            $servicePoint = $this->dbServicePointRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_service_points.singular')]));

            return redirect()->route('basicdata.service_points.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_service_points.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified ServicePoint.
     */
    public function show($id)
    {
        $servicePoint = $this->dbServicePointRepository->find($id);

        if (empty($servicePoint)) {
            flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.service_points.index'));
        }

        return view('basicdata::service_points.show')->with('servicePoint', $servicePoint);
    }

    /**
     * Show the form for editing the specified ServicePoint.
     */
    public function edit($id)
    {
        $servicePoint = $this->dbServicePointRepository->find($id);
          $data['statuses'] = $this->dbServicePointRepository->statuses();
          $data['types'] = $this->dbServicePointRepository->types();
            $data['servicePoint'] = $servicePoint;

        if (empty($servicePoint)) {
            flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.service_points.index'));
        }

        return view('basicdata::service_points.edit',$data);
    }

    /**
     * Update the specified Unit in storage.
     *
     * @param int $id
     * @param UpdateDbUnitRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDbServicePointRequest $request, $id)
    {
        try {
            $servicePoint = $this->dbServicePointRepository->find($id);

            if (empty($servicePoint)) {
                flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.service_points.index'));
            }

            $input = $request->all();
            $servicePoint = $this->dbServicePointRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_service_points.singular')]));

            return redirect()->route('basicdata.service_points.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_service_points.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified ServicePoint from storage.
     */
    public function destroy($id)
    {
        try {
            $servicePoint = $this->dbServicePointRepository->find($id);

            if (empty($servicePoint)) {
                flash()->error(__('basicdata::models/db_service_points.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.service_points.index'));
            }

            $this->dbServicePointRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_service_points.singular')]));

            return redirect()->route('basicdata.service_points.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_service_points.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }


            public function excel()
    {
        $headers = $this->dbServicePointRepository->header();
        $dataExcel = $this->dbServicePointRepository->dataExel(); // استخدم Unit بدلاً من dataExel

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'service_points.xlsx');
    }

    public function csv()
    {
        $headers = $this->dbServicePointRepository->header();
        $dataExcel = $this->dbServicePointRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'service_points.csv');
    }

    public function pdf()
    {
         $headers = $this->dbServicePointRepository->header();
        $dataExcel = $this->dbServicePointRepository->dataExel();
          $name = $this->dbServicePointRepository->name();


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
