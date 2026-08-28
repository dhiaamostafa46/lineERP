<?php

namespace Modules\Finance\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Finance\App\Models\FncBond;
use App\Models\AccuSoft\TreeAccounts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\FiscalYear;
use App\Models\User;
use App\Services\AccuSoft\JournalEntryService;
use Modules\Finance\App\Repositories\FncBondRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BasicData\App\Exports\BasicDataExport;
use Mpdf\Mpdf;

class FncBondController extends Controller
{
    private $journalEntryService;
    private $FncBondRepository;

    public function __construct(FncBondRepository $FncBondRepository, JournalEntryService $journalEntryService)
    {
        $this->FncBondRepository = $FncBondRepository;
        $this->journalEntryService = $journalEntryService;
    }

    public function excel()
    {
        $headers = $this->FncBondRepository->header();
        $dataExcel = $this->FncBondRepository->dataExel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'bonds.xlsx');
    }

    public function csv()
    {
        $headers = $this->FncBondRepository->header();
        $dataExcel = $this->FncBondRepository->dataExel();
        return Excel::download(new BasicDataExport($dataExcel, $headers), 'bonds.csv');
    }

    public function pdf()
    {
        $headers = $this->FncBondRepository->header();
        $dataExcel = $this->FncBondRepository->dataExel();
        $name = __('finance::models/fnc_bond.plural');

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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['bonds'] = $this->FncBondRepository->allQuery($request->except('pagination'))->latest()->paginate(10);
        $data['statuses'] = $this->FncBondRepository->statuses();
        $data['types'] = $this->FncBondRepository->types();

        return view('finance::bonds.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['fundAccounts'] = $this->FncBondRepository->fundAccounts();
        $data['contactAccounts'] = $this->FncBondRepository->contactAccounts();
        $data['costCenters'] = $this->FncBondRepository->costCenters();
        $data['branches'] = $this->FncBondRepository->branches();
        $data['types'] = $this->FncBondRepository->types();
        $data['statuses'] = $this->FncBondRepository->statuses();
        return view('finance::bonds.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bond_type' => 'required|in:' . FncBond::TYPE_PAYMENT . ',' . FncBond::TYPE_RECEIPT,
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:100',
            'fund_account_id' => 'required|exists:tree_accounts,id',
            'contact_account_id' => 'required|exists:tree_accounts,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:' . FncBond::STATUS_DRAFT . ',' . FncBond::STATUS_APPROVED,
        ]);



        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['voucher_number'] = $this->generateVoucherNumber($request->bond_type);
            $input['created_by'] = auth()->id();
            $input['fiscal_year_id'] = FiscalYear::open()->first()->id ?? null;


            $bond = $this->FncBondRepository->create($input);

            if ($bond->status == FncBond::STATUS_APPROVED) {
                $this->createJournalEntryForBond($bond);
            }

            DB::commit();

            flash()->success(__('messages.saved', ['model' => __('finance::models/fnc_bond.singular')]));
            return redirect()->route('fnc.bonds.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_creating', ['model' => __('finance::models/fnc_bond.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bond = FncBond::with(['fundAccount', 'contactAccount', 'branch', 'costCenter'])->find($id);

        if (empty($bond)) {
            flash()->error(__('finance::models/fnc_bond.singular') . ' ' . __('messages.not_found'));
            return redirect(route('fnc.bonds.index'));
        }

        $org = $this->FncBondRepository->getdataorganization();
        return view('finance::bonds.show', compact('bond', 'org'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bond = $this->FncBondRepository->find($id);

        if (empty($bond)) {
            flash()->error(__('finance::models/fnc_bond.singular') . ' ' . __('messages.not_found'));
            return redirect(route('fnc.bonds.index'));
        }

        $data['fundAccounts'] = $this->FncBondRepository->fundAccounts();
        $data['contactAccounts'] = $this->FncBondRepository->contactAccounts();
        $data['costCenters'] = $this->FncBondRepository->costCenters();
        $data['branches'] = $this->FncBondRepository->branches();
        $data['types'] = $this->FncBondRepository->types();
        $data['statuses'] = $this->FncBondRepository->statuses();
        $data['bond'] = $bond;

        return view('finance::bonds.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $bond = $this->FncBondRepository->find($id);

            if (empty($bond)) {
                flash()->error(__('finance::models/fnc_bond.singular') . ' ' . __('messages.not_found'));
                return redirect(route('fnc.bonds.index'));
            }

            $validator = Validator::make($request->all(), [
                'bond_type' => 'required|in:' . FncBond::TYPE_PAYMENT . ',' . FncBond::TYPE_RECEIPT,
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
                'reference_number' => 'nullable|string|max:100',
                'fund_account_id' => 'required|exists:tree_accounts,id',
                'contact_account_id' => 'required|exists:tree_accounts,id',
                'cost_center_id' => 'nullable|exists:cost_centers,id',
                'branch_id' => 'nullable|exists:branches,id',
                'description' => 'nullable|string|max:500',
                'status' => 'required|in:' . FncBond::STATUS_DRAFT . ',' . FncBond::STATUS_APPROVED,
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $oldStatus = $bond->status;

            // Perform the update
            $bond = $this->FncBondRepository->update($request->all(), $id);

            // If status changed to approved and no journal entry exists, create one
            if ($bond->status == FncBond::STATUS_APPROVED && $oldStatus != FncBond::STATUS_APPROVED) {
                $this->createJournalEntryForBond($bond);
            }
            // If status changed from approved to something else, delete or reverse journal entry
            elseif ($bond->status != FncBond::STATUS_APPROVED && $oldStatus == FncBond::STATUS_APPROVED && $bond->journal_entry_id) {
                $journalEntry = JournalEntry::find($bond->journal_entry_id);
                if ($journalEntry) {
                    if ($journalEntry->status == JournalEntry::STATUS_DRAFT) {
                        $journalEntry->delete();
                    } else {
                        // Mark as reversed if already posted
                        $journalEntry->status = JournalEntry::STATUS_REVERSED;
                        $journalEntry->save();
                    }
                }
                $bond->update(['journal_entry_id' => null]);
            }
            // If bond was approved and updated, and journal entry exists, update it (if possible)
            elseif ($bond->status == FncBond::STATUS_APPROVED && $oldStatus == FncBond::STATUS_APPROVED && $bond->journal_entry_id) {
                $this->updateJournalEntryForBond($bond);
            }

            DB::commit();

            flash()->success(__('messages.updated', ['model' => __('finance::models/fnc_bond.singular')]));
            return redirect()->route('fnc.bonds.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_updating', ['model' => __('finance::models/fnc_bond.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $bond = $this->FncBondRepository->find($id);

            if (empty($bond)) {
                flash()->error(__('messages.not_found', ['model' => __('finance::models/fnc_bond.singular')]));
                return redirect(route('fnc.bonds.index'));
            }

            DB::beginTransaction();

            if ($bond->journal_entry_id) {
                $journalEntry = JournalEntry::find($bond->journal_entry_id);
                if ($journalEntry) {
                    if ($journalEntry->status == JournalEntry::STATUS_DRAFT) {
                        $journalEntry->delete();
                    } else {
                        // Mark as reversed if already posted
                        $journalEntry->status = JournalEntry::STATUS_REVERSED;
                        $journalEntry->save();
                    }
                }
            }

            $this->FncBondRepository->delete($id);

            DB::commit();

            flash()->success(__('messages.deleted', ['model' => __('finance::models/fnc_bond.singular')]));
            return redirect()->route('fnc.bonds.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error(__('messages.error_deleting', ['model' => __('finance::models/fnc_bond.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Generate a unique voucher number.
     */
    private function generateVoucherNumber(int $bondType): string
    {
        $prefix = $bondType == FncBond::TYPE_PAYMENT ? 'PV' : 'RV'; // Payment Voucher / Receipt Voucher
        $year = date('Y');
        $lastBond = FncBond::withTrashed()->where('bond_type', $bondType)
            ->where('voucher_number', 'like', "{$prefix}-{$year}-%")
            ->latest('id')
            ->first();

        $nextNumber = 1;
        if ($lastBond) {
            $lastNumber = (int) substr($lastBond->voucher_number, -6); // Assuming 6 digits for sequence
            $nextNumber = $lastNumber + 1;
        }

        return "{$prefix}-{$year}-" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }














































































































    /**
     * Create a journal entry for the given bond.
     */
    private function createJournalEntryForBond(FncBond $bond): void
    {
        // Check if a journal entry already exists for this bond
        if ($bond->journal_entry_id) {
            $this->updateJournalEntryForBond($bond);
            return;
        }

        $fiscalYear = FiscalYear::checkDate($bond->date); // Use checkDate
        if (!$fiscalYear) {
            throw new \Exception(__('accusoft::general.date_outside_open_fiscal_year'));
        }

        $entryData = $this->prepareJournalEntryData($bond, $fiscalYear);
        $journalEntry = $this->journalEntryService->createJournalEntryFor($entryData);

        // Avoid infinite loops where update hooks may run
        FncBond::where('id', $bond->id)->update(['journal_entry_id' => $journalEntry->id, 'fiscal_year_id' => $fiscalYear->id]);
    }

    /**
     * Update an existing journal entry for the given bond.
     */
    private function updateJournalEntryForBond(FncBond $bond): void
    {
        $journalEntry = JournalEntry::find($bond->journal_entry_id);

        if (!$journalEntry) {
            $this->createJournalEntryForBond($bond); // If for some reason it's missing, create it
            return;
        }

        if ($journalEntry->status == JournalEntry::STATUS_POSTED && $journalEntry->is_locked) {
            throw new \Exception('لا يمكن تعديل القيد المحاسبي المرتبط بسند معتمد ومغلق.');
        }

        $fiscalYear = FiscalYear::checkDate($bond->date); // Use checkDate
        if (!$fiscalYear) {
            throw new \Exception(__('accusoft::general.date_outside_open_fiscal_year'));
        }

        $entryData = $this->prepareJournalEntryData($bond, $fiscalYear);
        // الحفاظ على رقم القيد الأصلي عند التحديث
        $entryData['entry_number'] = $journalEntry->entry_number;

        $this->journalEntryService->updateJournalEntryFor($journalEntry->id, $entryData);

        FncBond::where('id', $bond->id)->update(['fiscal_year_id' => $fiscalYear->id]);
    }

    /**
     * Prepare common journal entry data structure for the service.
     */
    private function prepareJournalEntryData(FncBond $bond, FiscalYear $fiscalYear): array
    {
        $details = [];

        if ($bond->bond_type == FncBond::TYPE_PAYMENT) {
            // سند صرف: مدين لحساب الجهة، دائن لحساب الصندوق/البنك
            $details[] = [
                'tree_account_id' => $bond->contact_account_id,
                'cost_center_id'  => $bond->cost_center_id,
                'debit'           => $bond->amount,
                'credit'          => 0,
                'description'     => $bond->description,
            ];
            $details[] = [
                'tree_account_id' => $bond->fund_account_id,
                'cost_center_id'  => $bond->cost_center_id,
                'debit'           => 0,
                'credit'          => $bond->amount,
                'description'     => $bond->description,
            ];
        } else {
            // سند قبض: مدين لحساب الصندوق/البنك، دائن لحساب الجهة
            $details[] = [
                'tree_account_id' => $bond->fund_account_id,
                'cost_center_id'  => $bond->cost_center_id,
                'debit'           => $bond->amount,
                'credit'          => 0,
                'description'     => $bond->description,
            ];
            $details[] = [
                'tree_account_id' => $bond->contact_account_id,
                'cost_center_id'  => $bond->cost_center_id,
                'debit'           => 0,
                'credit'          => $bond->amount,
                'description'     => $bond->description,
            ];
        }

        return [
            'entry_number'   => JournalEntry::generateEntryNumber(),
            'entry_date' => $bond->date,
            'description' => $bond->description ?? ($bond->bond_type == FncBond::TYPE_PAYMENT ? 'سند صرف' : 'سند قبض') . ' رقم ' . $bond->voucher_number,
            'fiscal_year_id' => $fiscalYear->id,
            'entry_type'     => JournalEntry::ENTRY_TYPE_AUTO,
            'source'         => JournalEntry::SOURCE_FINANCE,
            'status' => JournalEntry::STATUS_POSTED, // Ensure it's posted if bond is approved
            'created_by'     => auth()->id(),
            'posted_by' => auth()->id(),
            'posted_at' => now(),
            'reference_type' => FncBond::class,
            'reference_id'   => $bond->id,
            'details'        => $details,
        ];
    }
}
