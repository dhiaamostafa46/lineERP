<?php

namespace Modules\Pos\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\Pos\App\Http\Requests\CreatePosPaymentMethodRequest;
use Modules\Pos\App\Http\Requests\UpdatePosPaymentMethodRequest;
use Modules\Pos\App\Repositories\PosPaymentMethodRepository;
use Maatwebsite\Excel\Facades\Excel;

class PosPaymentMethodController extends AppBaseController
{
    private $posPaymentMethodRepository;

    public function __construct(PosPaymentMethodRepository $posPaymentMethodRepo)
    {
        $this->posPaymentMethodRepository = $posPaymentMethodRepo;
    }

    public function index(Request $request)
    {
        $data['paymentMethods'] = $this->posPaymentMethodRepository->allQuery($request->except('pagination'))->latest()->paginate($request->input('pagination', 10));


        return view('pos::payment_methods.index', $data);
    }

    public function create()
    {
        $data = [];
        return view('pos::payment_methods.create', $data);
    }

    public function store(CreatePosPaymentMethodRequest $request)
    {
        try {
            $input = $request->except(['_token', '_method']);
            
            $paymentMethod = $this->posPaymentMethodRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('pos::models/payment_methods.singular')]));

            return redirect()->route('pos.payment_methods.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('pos::models/payment_methods.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $paymentMethod = $this->posPaymentMethodRepository->find($id);
        
        if (empty($paymentMethod)) {
            flash()->error(__('pos::models/payment_methods.singular') . ' ' . __('messages.not_found'));
            return redirect(route('pos.payment_methods.index'));
        }


        $data['paymentMethod'] = $paymentMethod;

        return view('pos::payment_methods.edit', $data);
    }

    public function update(UpdatePosPaymentMethodRequest $request, $id)
    {
        try {
            $paymentMethod = $this->posPaymentMethodRepository->find($id);

            if (empty($paymentMethod)) {
                flash()->error(__('pos::models/payment_methods.singular') . ' ' . __('messages.not_found'));
                return redirect(route('pos.payment_methods.index'));
            }

            $input = $request->except(['_token', '_method']);
            
            $paymentMethod = $this->posPaymentMethodRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('pos::models/payment_methods.singular')]));

            return redirect()->route('pos.payment_methods.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('pos::models/payment_methods.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $paymentMethod = $this->posPaymentMethodRepository->find($id);

            if (empty($paymentMethod)) {
                flash()->error(__('pos::models/payment_methods.singular') . ' ' . __('messages.not_found'));
                return redirect(route('pos.payment_methods.index'));
            }

            $this->posPaymentMethodRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('pos::models/payment_methods.singular')]));

            return redirect()->route('pos.payment_methods.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('pos::models/payment_methods.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function excel()
    {
        $headers = $this->posPaymentMethodRepository->header();
        $dataExcel = $this->posPaymentMethodRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'payment_methods.xlsx');
    }

    public function csv()
    {
        $headers = $this->posPaymentMethodRepository->header();
        $dataExcel = $this->posPaymentMethodRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'payment_methods.csv');
    }

    public function pdf()
    {
        $headers = $this->posPaymentMethodRepository->header();
        $dataExcel = $this->posPaymentMethodRepository->dataExel();
        $name = $this->posPaymentMethodRepository->name();

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
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        
        $mpdf->WriteHTML(view('basicdata::exports.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name]));
        $mpdf->Output();
    }
}
