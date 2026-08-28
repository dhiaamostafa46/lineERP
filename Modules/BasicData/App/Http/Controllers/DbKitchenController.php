<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\BasicData\App\Http\Requests\CreateDbKitchenRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbKitchenRequest;
use Modules\BasicData\App\Repositories\DbKitchenRepository;

class DbKitchenController extends AppBaseController
{
    /** @var DbKitchenRepository $dbKitchenRepository*/
    private $dbKitchenRepository;

    public function __construct(DbKitchenRepository $dbKitchenRepo)
    {
        $this->dbKitchenRepository = $dbKitchenRepo;
    }

    /**
     * Display a listing of the Kitchen.
     */
    public function index(Request $request)
    {
        $data['kitchens'] = $this->dbKitchenRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['statuses'] = $this->dbKitchenRepository->statuses();
        return view('basicdata::kitchens.index', $data);
    }

    /**
     * Show the form for creating a new Kitchen.
     */
    public function create()
    {
           $data['statuses'] = $this->dbKitchenRepository->statuses();
        return view('basicdata::kitchens.create' , $data);
    }

    /**
     * Store a newly created Kitchen in storage.
     */
    public function store(CreateDbKitchenRequest $request)
    {
        // try {
            $input = $request->all();

            $kitchen = $this->dbKitchenRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_kitchens.singular')]));

            return redirect()->route('basicdata.kitchens.index');
        // } catch (\Exception $e) {
        //     flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_kitchens.singular')]) . ': ' . $e->getMessage());
        //     return redirect()->back()->withInput();
        // }
    }

    /**
     * Display the specified Kitchen.
     */
    public function show($id)
    {
        $kitchen = $this->dbKitchenRepository->find($id);

        if (empty($kitchen)) {
            flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.kitchens.index'));
        }

        return view('basicdata::kitchens.show')->with('kitchen', $kitchen);
    }

    /**
     * Show the form for editing the specified Kitchen.
     */
    public function edit($id)
    {
        $kitchen = $this->dbKitchenRepository->find($id);
          $data['statuses'] = $this->dbKitchenRepository->statuses();
            $data['kitchen'] = $kitchen;

        if (empty($kitchen)) {
            flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.kitchens.index'));
        }

        return view('basicdata::kitchens.edit',$data);
    }

    /**
     * Update the specified Unit in storage.
     *
     * @param int $id
     * @param UpdateDbUnitRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDbKitchenRequest $request, $id)
    {
        try {
            $kitchen = $this->dbKitchenRepository->find($id);

            if (empty($kitchen)) {
                flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.kitchens.index'));
            }

            $input = $request->all();
            $kitchen = $this->dbKitchenRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_kitchens.singular')]));

            return redirect()->route('basicdata.kitchens.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_kitchens.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Kitchen from storage.
     */
    public function destroy($id)
    {
        try {
            $kitchen = $this->dbKitchenRepository->find($id);

            if (empty($kitchen)) {
                flash()->error(__('basicdata::models/db_kitchens.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.kitchens.index'));
            }

            $this->dbKitchenRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_kitchens.singular')]));

            return redirect()->route('basicdata.kitchens.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_kitchens.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }


    public function excel()
    {
        $headers = $this->dbKitchenRepository->header();
        $dataExcel = $this->dbKitchenRepository->dataExel(); // استخدم Unit بدلاً من dataExel

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'kitchens.xlsx');
    }

    public function csv()
    {
        $headers = $this->dbKitchenRepository->header();
        $dataExcel = $this->dbKitchenRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'kitchens.csv');
    }

    public function pdf()
    {
         $headers = $this->dbKitchenRepository->header();
        $dataExcel = $this->dbKitchenRepository->dataExel();
          $name = $this->dbKitchenRepository->name();


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
