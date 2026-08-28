<?php

namespace App\Http\Controllers;

use App\Exports\GlobalDataExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccuSoft\TaxAccount;
use Illuminate\Support\Facades\Validator;
use App\Repositories\TaxAccountRepository;
// use Modules\AccuSoft\App\Repositories\AsTaxAccountRepository;
// use Modules\AccuSoft\App\Exports\AccuSoftDataExport;
use Maatwebsite\Excel\Facades\Excel;

class TaxAccountController extends Controller
{
    private $asTaxAccountRepository;

    public function __construct(TaxAccountRepository $asTaxAccountRepo)
    {
        $this->asTaxAccountRepository = $asTaxAccountRepo;
    }

    public function index(Request $request)
    {
        $data['taxAccounts'] = $this->asTaxAccountRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['statuses'] = $this->asTaxAccountRepository->statuses();
        return view('tax_accounts.index', $data);
    }

    public function create()
    {
        $data['statuses'] = $this->asTaxAccountRepository->statuses();
        return view('tax_accounts.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), TaxAccount::rules());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->asTaxAccountRepository->create($request->all());

            flash()->success(__('messages.saved', ['model' => __('models/tax_accounts.singular')]));

            return redirect()->route('taxaccounts.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('models/tax_accounts.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $taxAccount = $this->asTaxAccountRepository->find($id);

        if (empty($taxAccount)) {
            flash()->error(__('models/tax_accounts.singular') . ' ' . __('messages.not_found'));
            return redirect(route('taxaccounts.index'));
        }

        return view('tax_accounts.show')->with('taxAccount', $taxAccount);
    }

    public function edit($id)
    {
        $taxAccount = $this->asTaxAccountRepository->find($id);
        $data['statuses'] = $this->asTaxAccountRepository->statuses();
        $data['taxAccount'] = $taxAccount;

        if (empty($taxAccount)) {
            flash()->error(__('models/tax_accounts.singular') . ' ' . __('messages.not_found'));
            return redirect(route('taxaccounts.index'));
        }

        return view('tax_accounts.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            $taxAccount = $this->asTaxAccountRepository->find($id);

            if (empty($taxAccount)) {
                flash()->error(__('models/tax_accounts.singular') . ' ' . __('messages.not_found'));
                return redirect(route('accusoft.TaxAccount.index'));
            }

            $validator = Validator::make($request->all(), TaxAccount::rules($id));

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $this->asTaxAccountRepository->update($request->all(), $id);

            flash()->success(__('messages.updated', ['model' => __('models/tax_accounts.singular')]));

            return redirect()->route('taxaccounts.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('models/tax_accounts.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        // try {
        //     $taxAccount = $this->asTaxAccountRepository->find($id);

        //     if (empty($taxAccount)) {
        //         flash()->error(__('models/tax_accounts.singular') . ' ' . __('messages.not_found'));
        //         return redirect(route('taxaccounts.index'));
        //     }

        //     $this->asTaxAccountRepository->delete($id);

        //     flash()->success(__('messages.deleted', ['model' => __('models/tax_accounts.singular')]));

        //     return redirect()->route('taxaccounts.index');
        // } catch (\Exception $e) {
        //     flash()->error(__('messages.error_deleting', ['model' => __('models/tax_accounts.singular')]) . ': ' . $e->getMessage());
        //     return redirect()->back();
        // }
    }

    public function excel()
    {
        $headers = $this->asTaxAccountRepository->getHeaders();
        $dataExcel = $this->asTaxAccountRepository->dataExcel();
        return Excel::download(new GlobalDataExport($dataExcel, $headers), 'TaxAccounts.xlsx');
    }

    public function csv()
    {
        $headers = $this->asTaxAccountRepository->getHeaders();
        $dataExcel = $this->asTaxAccountRepository->dataExcel();
        return Excel::download(new GlobalDataExport($dataExcel, $headers), 'TaxAccounts.csv');
    }

    public function pdf()
    {
        $headers = $this->asTaxAccountRepository->getHeaders();
        $dataExcel = $this->asTaxAccountRepository->dataExcel();
        $name = $this->asTaxAccountRepository->name();

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
        $mpdf->WriteHTML(view('exports.pdf', ['headers' => $headers, 'data' => $dataExcel, 'name' => $name]));
        $mpdf->Output();
    }
}
