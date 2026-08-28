<?php

namespace Modules\Finance\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Modules\AccuSoft\App\Repositories\AsTreeAccountsRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;

class SafeController extends Controller
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
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'safes.xlsx');
    }

    public function csv()
    {
        $headers = $this->getHeaders();
        $dataExcel = $this->dataExcel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'safes.csv');
    }

    public function pdf()
    {
        $headers = $this->getHeaders();
        $dataExcel = $this->dataExcel();
        $name = __('finance::models/fnc_safe.plural');

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
            __('finance::models/fnc_safe.fields.name'),
            __('finance::models/fnc_safe.fields.status'),
            __('finance::models/fnc_safe.fields.created_at'),
        ];
    }

    private function dataExcel(): array
    {
        return TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_TREASURY)
            ->where('is_leaf', true)
            ->with('translations')
            ->get()
            ->map(function ($safe) {
                return [
                    'name' => $safe->name,
                    'status' => $safe->status_text,
                    'created_at' => $safe->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    private function getRetainedEarningsAccount()
    {
        $account = TreeAccounts::where('account_type', TreeAccounts::ACCOUNT_TYPE_TREASURY)->first();
        return $account;
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $data['safes'] = $this->asTreeAccountsRepository->allQuery($request->except('pagination'))->where('account_type', TreeAccounts::ACCOUNT_TYPE_TREASURY)->where('is_leaf', true)->latest()->paginate(10);

        return view('finance::safes.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['statuses'] = $this->asTreeAccountsRepository->statuses();
        return view('finance::safes.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), []);



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
                $input['code'] = TreeAccounts::generateCode($input['parent_id']);
            }

            $input['account_type'] = TreeAccounts::ACCOUNT_TYPE_TREASURY;
            $input['type'] = TreeAccounts::TYPE_DEBIT;
            $input['is_leaf'] = true;


            $input['attributes'] = [
                'payment_method' => $request->get('payment_method', array_key_first(config('payment_methods.cash'))),
            ];



            $this->asTreeAccountsRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('finance::models/fnc_safe.singular')]));

            return redirect()->route('fnc.safes.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating') . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        $bank = $this->asTreeAccountsRepository->find($id);

        if (empty($bank)) {
            flash()->error(__('finance::models/fnc_safe.singular') . ' ' . __('messages.not_found'));
            return redirect(route('fnc.safes.index'));
        }
        return view('finance::safes.show')->with('bank', $bank);
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
            flash()->error(__('finance::models/fnc_safe.singular') . ' ' . __('messages.not_found'));
            return redirect(route('fnc.safes.index'));
        }

        return view('finance::safes.edit', $data);
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
                return redirect(route('fnc.safes.index'));
            }

            foreach (config('langs') as $locale => $language) {
                $rules[$locale . '.name'] = 'required|string|max:255';
            }

            $validator = Validator::make($request->all(), []);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $input = $request->all();
            $asTreeAccount = $this->asTreeAccountsRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('finance::models/fnc_safe.singular')]));

            return redirect()->route('fnc.safes.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('finance::models/fnc_safe.singular')]) . ': ' . $e->getMessage());
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
                flash()->error(__('messages.not_found', ['model' => __('finance::models/fnc_safe.singular')]));
                return redirect(route('fnc.safes.index'));
            }

            $this->asTreeAccountsRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('finance::models/fnc_safe.singular')]));

            return redirect()->route('fnc.safes.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('finance::models/fnc_safe.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
