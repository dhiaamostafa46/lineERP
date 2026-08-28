<?php

namespace Modules\AccuSoft\App\Imports;

use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\AccuSoft\CostCenters;
use App\Services\AccuSoft\JournalEntryService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalEntryImport implements ToCollection, WithHeadingRow
{
    private $errors = [];
    private $successCount = 0;
    private $service;

    public function __construct()
    {
        $this->service = app(JournalEntryService::class);
    }

    public function collection(Collection $rows)
    {
        $groupedEntries = $this->groupRows($rows);

        if ($groupedEntries->isEmpty()) {
            throw new Exception("لم يتم العثور على أي بيانات صحيحة في الملف. يرجى التأكد من أن الملف يحتوي على عمود 'Trans #' وأن البيانات تبدأ من الصف الثاني.");
        }

        foreach ($groupedEntries as $transNo => $entryData) {
            try {
                DB::beginTransaction();

                $validationErrors = $this->validateEntry($entryData);
                if (!empty($validationErrors)) {
                    $this->addErrors($entryData['rows'], $validationErrors);
                    DB::rollBack();
                    continue;
                }

                $this->service->create($this->mapToServiceData($entryData));

                $this->successCount++;
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->addErrors($entryData['rows'], [$e->getMessage()]);
            }
        }
    }

    private function groupRows(Collection $rows)
    {
        $entries = [];
        $currentTransNo = null;

        foreach ($rows as $index => $row) {
            // Convert row keys to normalized format for better matching
            $normalizedRow = [];
            foreach ($row as $key => $value) {
                $cleanKey = strtolower(preg_replace('/[^a-z0-9]/', '', $key));
                $normalizedRow[$cleanKey] = $value;
            }

            // Header Fields for grouping
            $journalCode = trim($normalizedRow['journalcode'] ?? $normalizedRow['trans'] ?? $normalizedRow['transno'] ?? $normalizedRow['transn'] ?? $normalizedRow['a'] ?? '');
            $journalDate = trim($normalizedRow['journaldate'] ?? $normalizedRow['date'] ?? $normalizedRow['c'] ?? '');
            $branchStr = trim($normalizedRow['branch'] ?? '');
            $journalDesc = trim($normalizedRow['journaldescription'] ?? $normalizedRow['memo'] ?? $normalizedRow['h'] ?? '');

            if (!empty($journalCode)) {
                // Generate a unique grouping key based on the 4 header fields
                $currentTransNo = md5($journalCode . '|' . $journalDate . '|' . $branchStr . '|' . $journalDesc);

                if (!isset($entries[$currentTransNo])) {
                    $entries[$currentTransNo] = [
                        'header' => [
                            'journalcode' => $journalCode,
                            'journaldate' => $journalDate,
                            'branch' => $branchStr,
                            'journaldescription' => $journalDesc
                        ],
                        'original_header' => $row,
                        'rows' => [],
                        'details' => []
                    ];
                }
            }

            if ($currentTransNo) {
                // Determine account code and name from the new separate columns, or fallback to the old unified string
                $accountCode = trim($normalizedRow['accountcode'] ?? '');
                $accountName = trim($normalizedRow['accountname'] ?? '');

                $accountStr = trim($normalizedRow['account'] ?? $normalizedRow['j'] ?? '');
                if (empty($accountStr) && (!empty($accountCode) || !empty($accountName))) {
                    // Create a simulated old-format string if new columns are used so downstream code can still parse it easily
                    // Or we just pass them directly. It's better to pass them in details.
                    $accountStr = $accountCode . ($accountName ? ' ▪ ' . $accountName : '');
                }

                $debit = $this->parseNumber($normalizedRow['debit'] ?? $normalizedRow['l'] ?? 0);
                $credit = $this->parseNumber($normalizedRow['credit'] ?? $normalizedRow['m'] ?? 0);

                // Name -> Old format used "name" for something else. Now we have "transactiondescription"
                $name = trim($normalizedRow['transactiondescription'] ?? $normalizedRow['name'] ?? $normalizedRow['i'] ?? '');

                // Memo -> Journal Description
                $memo = trim($normalizedRow['journaldescription'] ?? $normalizedRow['memo'] ?? $normalizedRow['h'] ?? '');

                $costCenterStr = trim($normalizedRow['costcenter'] ?? '');
                $branchStr = trim($normalizedRow['branch'] ?? '');

                if (empty($accountStr) && empty($accountCode) && empty($accountName) && ($debit > 0 || $credit > 0)) {
                    continue;
                }

                if (!empty($accountStr) || !empty($accountCode) || !empty($accountName) || $debit > 0 || $credit > 0) {
                    $entries[$currentTransNo]['rows'][] = $row;
                    $entries[$currentTransNo]['details'][] = [
                        'account_str' => $accountStr,
                        'accountcode' => $accountCode,
                        'accountname' => $accountName,
                        'debit' => $debit,
                        'credit' => $credit,
                        'memo' => $memo,
                        'name' => $name,
                        'cost_center_str' => $costCenterStr,
                        'branch_str' => $branchStr,
                        'row_index' => $index + 2
                    ];
                }
            }
        }

        return collect($entries);
    }

    private function validateEntry(array $entryData)
    {
        $errors = [];
        $details = $entryData['details'];

        if (empty($details)) {
            $errors[] = "القيد لا يحتوي على تفاصيل (أسطر).";
            return $errors;
        }
        
        if (count($details) < 2) {
            $errors[] = "القيد يحتوي على سطر واحد فقط. يجب أن يتكون القيد من سطرين على الأقل (طرف مدين وطرف دائن) ليكون متوازناً.";
        }

        $entryNumber = trim($entryData['header']['journalcode'] ?? '');
        if (!empty($entryNumber)) {
            $exists = JournalEntry::where('entry_number', $entryNumber)->exists();
            if ($exists) {
                $errors[] = "رقم القيد '$entryNumber' مكرر وموجود مسبقاً في النظام.";
            }
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($details as $detail) {
            $totalDebit += $detail['debit'];
            $totalCredit += $detail['credit'];

            // Validate Account (Try Code then Name based on new columns)
            $accountCode = trim($detail['accountcode'] ?? '');
            $accountName = trim($detail['accountname'] ?? '');

            // Fallback to extraction from combined string if old format is used
            if (empty($accountCode) && empty($accountName)) {
                $accountCode = $this->extractAccountCode($detail['account_str'] ?? '');
                $accountName = $this->extractAccountName($detail['account_str'] ?? '');
            }

            $account = null;
            if (!empty($accountCode)) {
                $account = TreeAccounts::where('code', $accountCode)->first();
            }
            if (!$account && !empty($accountName)) {
                $account = TreeAccounts::whereTranslation('name', $accountName)->first();
            }

            if (!$account) {
                $errors[] = "السطر في الصف " . $detail['row_index'] . ": الحساب ($accountCode - $accountName) غير موجود. يرجى التأكد من وجود الحساب أولاً.";
            }
        }

        // Validate Balance
        if (abs($totalDebit - $totalCredit) > 0.001) {
            $errors[] = "القيد غير متوازن. إجمالي المدين: $totalDebit إجمالي الدائن: $totalCredit.";
        }

        // Validate Date
        $date = $this->parseDate($entryData['header']['journaldate'] ?? '');
        if (!$date) {
            $errors[] = "تاريخ القيد غير صحيح أو مفقود.";
        }

        return $errors;
    }

    private function mapToServiceData(array $entryData)
    {
        $header = $entryData['header'];
        $details = [];

        // Find the best description for the entry
        $entryDescription = trim($header['memo'] ?? '');
        if (empty($entryDescription) && !empty($entryData['details'])) {
            $entryDescription = $entryData['details'][0]['memo'];
        }

        foreach ($entryData['details'] as $detail) {
            $accountCode = trim($detail['accountcode'] ?? '');
            $accountName = trim($detail['accountname'] ?? '');

            if (empty($accountCode) && empty($accountName)) {
                $accountCode = $this->extractAccountCode($detail['account_str'] ?? '');
                $accountName = $this->extractAccountName($detail['account_str'] ?? '');
            }

            $account = null;
            if (!empty($accountCode)) {
                $account = TreeAccounts::where('code', $accountCode)->first();
            }
            if (!$account && !empty($accountName)) {
                $account = TreeAccounts::whereTranslation('name', $accountName)->first();
            }

            $lineDescription = $detail['memo'];
            if (!empty($detail['name'])) {
                $lineDescription = trim($detail['name']) . ($lineDescription ? " - $lineDescription" : "");
            }

            $costCenterStr = $detail['cost_center_str'] ?? '';
            $costCenterId = null;
            if (!empty($costCenterStr)) {
                $costCenter = CostCenters::where('code', $costCenterStr)->orWhereTranslation('name', $costCenterStr)->first();
                if ($costCenter) {
                    $costCenterId = $costCenter->id;
                }
            }

            $details[] = [
                'tree_account_id' => $account->id,
                'debit' => $detail['debit'],
                'credit' => $detail['credit'],
                'description' => $lineDescription,
                'cost_center_id' => $costCenterId,
            ];
        }

        // Branch mapping could be added at the header level if branch exists
        $branchStr = trim($header['branch'] ?? '');
        $branchId = null;
        if (!empty($branchStr)) {
            $branch = \App\Models\Branch::whereTranslation('name', $branchStr)->first();
            if ($branch) {
                $branchId = $branch->id;
            }
        }

        return [
            'entry_date' => $this->parseDate($header['journaldate'] ?? ''),
            'description' => !empty($entryDescription) ? $entryDescription : 'استيراد من Excel',
            'entry_number' => trim($header['journalcode'] ?? ''),
            'entry_type' => JournalEntry::ENTRY_TYPE_MANUAL,
            'status' => JournalEntry::STATUS_POSTED,
            'branch_id' => $branchId,
            'details' => $details
        ];
    }

    private function extractAccountCode($str)
    {
        // Keep only digits from the string (removes text, spaces, symbols)
        // Example: " 15001001 ▪ البنك الاهلى " -> "15001001"
        return preg_replace('/[^0-9]/', '', $str);
    }

    private function extractAccountName($str)
    {
        // Example: " 15001001 ▪ البنك الاهلى SNB Bank "
        if (strpos($str, '▪') !== false) {
            $parts = explode('▪', $str);
            return trim($parts[1] ?? '');
        }
        return '';
    }

    private function parseNumber($val)
    {
        if (is_numeric($val))
            return (float) $val;
        $val = str_replace([',', ' '], '', $val);
        return is_numeric($val) ? (float) $val : 0;
    }

    private function parseDate($val)
    {
        if (empty($val))
            return null;
        try {
            if (is_numeric($val)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
            }

            // Try common formats
            $date = \DateTime::createFromFormat('d/m/Y', trim($val));
            if ($date)
                return $date->format('Y-m-d');

            $date = \DateTime::createFromFormat('m/d/Y', trim($val));
            if ($date)
                return $date->format('Y-m-d');

            return date('Y-m-d', strtotime($val));
        } catch (Exception $e) {
            return null;
        }
    }

    private function addErrors(array $rows, array $errorMessages)
    {
        $errorStr = implode(" | ", $errorMessages);
        foreach ($rows as $row) {
            $row['error_reason'] = $errorStr;
            $this->errors[] = $row;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}
