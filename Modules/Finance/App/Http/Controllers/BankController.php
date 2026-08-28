<?php

namespace Modules\Finance\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\AccountMapping;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Modules\AccuSoft\App\Repositories\AsTreeAccountsRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;

class BankController extends Controller
{
    private $asTreeAccountsRepository;

    public function __construct(AsTreeAccountsRepository $asTreeAccountsRepo)
    {
        $this->asTreeAccountsRepository = $asTreeAccountsRepo;
    }

    public function excel()
    {
        $headers = $this->getHeaders();
        $dataExcel = $this->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'banks.xlsx');
    }

    public function csv()
    {
        $headers = $this->getHeaders();
        $dataExcel = $this->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'banks.csv');
    }

    public function pdf()
    {
        $headers = $this->getHeaders();
        $dataExcel = $this->dataExcel();
        $name = __('finance::models/fnc_bank.plural');

        $mpdf = new Mpdf(['mode' => 'utf-8']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->autoArabic = true;
        $mpdf->SetDirectionality(app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
        $mpdf->WriteHTML(
            view('basicdata::exports.pdf', [
                'headers' => $headers,
                'data' => $dataExcel,
                'name' => $name,
            ]),
        );
        $mpdf->Output();
    }

    private function getHeaders(): array
    {
        return [
            __('finance::models/fnc_bank.fields.name'),
            __('finance::models/fnc_bank.fields.account_number'),
            __('finance::models/fnc_bank.fields.iban'),
            __('finance::models/fnc_bank.fields.status'),
            __('finance::models/fnc_bank.fields.created_at'),
        ];
    }

    private function dataExcel(): array
    {
        return TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_BANK)
            ->where('is_leaf', true)
            ->with('translations')
            ->get()
            ->map(function ($bank) {
                return [
                    'name' => $bank->name,
                    'account_number' => $bank->account_number ?? '---',
                    'iban' => $bank->iban ?? '---',
                    'status' => $bank->status_text,
                    'created_at' => $bank->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    private function getRetainedEarningsAccount(): TreeAccounts
    {
        $account = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_BANK)->first();
        return $account;
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $data['banks'] = $this->asTreeAccountsRepository->allQuery($request->except('pagination'))->where('account_type', TreeAccounts::ACCOUNT_TYPE_BANK)->where('is_leaf', true)->latest()->paginate(10);

        return view('finance::banks.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['statuses'] = $this->asTreeAccountsRepository->statuses();
        return view('finance::banks.create', $data);
    }

    public function store(Request $request)
    {
        // try {
            $validator = Validator::make($request->all(), [
                'account_number' => 'required|numeric',
                'iban' => 'nullable|string|max:35',
                'payment_method' => 'required',
            ]);

            // إضافة قواعد التحقق للأسماء المترجمة
            foreach (config('langs') as $locale => $language) {
                $validator->getRules()[$locale . '.name'] = 'required|string|max:255';
            }

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();

           
            // تحديد الحساب الأب الافتراضي إن لم يوجد

            $defaultParent = $this->getRetainedEarningsAccount();
            if (!$request->has('parent_id')) {
                $input['parent_id'] = $defaultParent ? $defaultParent->id : null;
                $input['code'] = TreeAccounts::generateCode( $input['parent_id']);
            }


            $input['account_type'] = TreeAccounts::ACCOUNT_TYPE_BANK;
            $input['type'] = TreeAccounts::TYPE_DEBIT;
            $input['is_leaf'] = true;





            // تجهيز الحقول الإضافية في مصفوفة attributes
            $input['attributes'] = [
                'account_number' => $input['account_number'],
                'iban' => $input['iban'],
                'payment_method' => $request->payment_method ?? array_key_first(config('payment_methods.bank')),
                'currency' => $request->get('currency', 'SAR'),    
            ];

            $this->asTreeAccountsRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('finance::models/fnc_bank.singular')]));

            return redirect()->route('fnc.banks.index');


        // } catch (\Exception $e) {
        //     flash()->error(__('messages.error_creating') . ': ' . $e->getMessage());
        //     return redirect()->back()->withInput();
        // }
    }

    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        $bank = $this->asTreeAccountsRepository->find($id);

        if (empty($bank)) {
            flash()->error(__('finance::models/fnc_bank.singular') . ' ' . __('messages.not_found'));
            return redirect(route('fnc.banks.index'));
        }
        return view('finance::banks.show')->with('bank', $bank);
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $bank = $this->asTreeAccountsRepository->find($id);
        $data['statuses'] = $this->asTreeAccountsRepository->statuses();
        $data['bank'] = $bank;
        if (empty($bank)) {
            flash()->error(__('finance::models/fnc_bank.singular') . ' ' . __('messages.not_found'));
            return redirect(route('fnc.banks.index'));
        }

        return view('finance::banks.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        try {
            $bank = $this->asTreeAccountsRepository->find($id);

            if (empty($bank)) {
                flash()->error(__('finance::models/fnc_bank.singular') . ' ' . __('messages.not_found'));
                return redirect(route('fnc.banks.index'));
            }

            $rules = [
                'account_number' => 'required|numeric',
                'iban' => 'nullable|string|max:35',
                'payment_method' => 'required',
            ];

            foreach (config('langs') as $locale => $language) {
                $rules[$locale . '.name'] = 'required|string|max:255';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $input['attributes'] = [
                'account_number' => $request->account_number,
                'iban' => $request->iban,
                'payment_method' => $request->payment_method ?? array_key_first(config('payment_methods.bank')),
            ];

            $asTreeAccount = $this->asTreeAccountsRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('finance::models/fnc_bank.singular')]));

            return redirect()->route('fnc.banks.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('finance::models/fnc_bank.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        try {
            $bank = $this->asTreeAccountsRepository->find($id);

            if (empty($bank)) {
                flash()->error(__('messages.not_found', ['model' => __('finance::models/fnc_bank.singular')]));
                return redirect(route('fnc.banks.index'));
            }

            $this->asTreeAccountsRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('finance::models/fnc_bank.singular')]));

            return redirect()->route('fnc.banks.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('finance::models/fnc_bank.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
