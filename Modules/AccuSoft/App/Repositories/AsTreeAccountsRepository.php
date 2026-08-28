<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\TreeAccounts;
use App\Repositories\BaseRepository;

class AsTreeAccountsRepository extends BaseRepository
{
    protected $fieldSearchable = [

        'code',
        'account_type', // 1=asset, 2=liability, 3=equity, 4=revenue, 5=expense, 6=cost_of_sales
        'parent_id',
        'level',
        'is_leaf',
        'status',
        'is_system',
        'attributes',
        'type', // 1=debit, 2=credit

    ];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.TreeAccounts';

        if (auth()->check()) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.user_id', auth()->id());
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'created_by') && !auth()->user()->can($permissionPrefix . '.scopedaccess')) {
                $query->where($table . '.created_by', auth()->id());
            }


        }

        return $query;
    }

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function Ajex()
    {
        return TreeAccounts::active()->with('translations')->get()->pluck('name', 'id')->toArray();
    }

    public function model(): string
    {
        return TreeAccounts::class;
    }

    public function statuses(): array
    {
        return TreeAccounts::statuses();
    }

    public function Root($request)
    {
        return TreeAccounts::whereNull('parent_id')->with('translations')->get();
    }

    public function types(): array
    {
        return TreeAccounts::types();
    }

    public function accountTypes(): array
    {
        return TreeAccounts::accountTypes();
    }

    public function TreeAccounts()
    {
        return TreeAccounts::active()->with('translations')->get()->pluck('name', 'id')->toArray();
    }

    public function listItems($id)
    {
        return TreeAccounts::findOrFail($id);
    }

    public function header(): array
    {
        return [
            __('accusoft::models/as_tree_account.fields.code'),
            __('accusoft::models/as_tree_account.fields.name'),
            __('accusoft::models/as_tree_account.fields.account_type'),
            __('accusoft::models/as_tree_account.fields.level'),
            __('accusoft::models/as_tree_account.fields.type'),
            __('accusoft::models/as_tree_account.fields.status'),

            __('accusoft::models/as_tree_account.fields.created_at'),
        ];
    }

    public function getHeaders(): array
    {
        return $this->header();
    }

    public function dataExcel(): array
    {
        return TreeAccounts::with('translations')
            ->get()
            ->map(function ($TreeAccounts) {
                return [
                    'code' => $TreeAccounts->code,
                    'name' => $TreeAccounts->name,
                    'account_type' => $TreeAccounts->account_type_text,
                    'level' => $TreeAccounts->level,
                    'type' => $TreeAccounts->type_text,
                    'status' => $TreeAccounts->status_text,
                    'created_at' => $TreeAccounts->created_at ? $TreeAccounts->created_at->format('Y-m-d') : '',
                ];
            })
            ->toArray();
    }

    public function downloadTemplate()
    {
        $fileName = 'chart_of_accounts_template.xlsx';
        $headers = ['code', 'name', 'account_type', 'type', 'parent_code', 'parent_name'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $index => $header) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
        }

        $accountTypeMap = \App\Models\AccuSoft\TreeAccounts::accountTypes();
        $typesDict = \App\Models\AccuSoft\TreeAccounts::types();

        $typeOptions = [
            $typesDict[\App\Models\AccuSoft\TreeAccounts::TYPE_DEBIT],
            $typesDict[\App\Models\AccuSoft\TreeAccounts::TYPE_CREDIT],
        ];

        $samples = [
            ['1', __('accusoft::models/as_tree_account.types.asset'), $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_ASSET], $typeOptions[0], '', ''],
            ['11', __('accusoft::models/as_tree_account.types.asset').' 1', $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_ASSET], $typeOptions[0], '1', $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_ASSET]],
            ['111', __('accusoft::models/as_tree_account.types.asset').' 1.1', $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_ASSET], $typeOptions[0], '11', $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_ASSET]],
            ['2', __('accusoft::models/as_tree_account.types.liability'), $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_LIABILITY], $typeOptions[1], '', ''],
            ['4', __('accusoft::models/as_tree_account.types.expense'), $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_EXPENSE], $typeOptions[0], '', ''],
            ['5', __('accusoft::models/as_tree_account.types.revenue'), $accountTypeMap[\App\Models\AccuSoft\TreeAccounts::ACCOUNT_TYPE_REVENUE], $typeOptions[1], '', ''],
        ];

        foreach ($samples as $rowIndex => $sample) {
            foreach ($sample as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 2, $value);
            }
        }

        $accountTypeComment = '';
        foreach ($accountTypeMap as $k => $v) {
            $accountTypeComment .= "$k = $v\n";
        }
        $sheet->getComment('C1')->getText()->createTextRun(trim($accountTypeComment));

        $typeComment = $typesDict[\App\Models\AccuSoft\TreeAccounts::TYPE_DEBIT]." = 1\n".$typesDict[\App\Models\AccuSoft\TreeAccounts::TYPE_CREDIT].' = 2';
        $sheet->getComment('D1')->getText()->createTextRun($typeComment);

        $listsSheet = $spreadsheet->createSheet();
        $listsSheet->setTitle('Lists');

        $rowIdx = 1;
        foreach ($accountTypeMap as $label) {
            $listsSheet->setCellValueByColumnAndRow(1, $rowIdx++, $label);
        }

        $listsSheet->setCellValueByColumnAndRow(2, 1, $typeOptions[0]);
        $listsSheet->setCellValueByColumnAndRow(2, 2, $typeOptions[1]);

        $listsSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        $accountTypeRange = 'Lists!$A$1:$A$'.count($accountTypeMap);
        $typeRange = 'Lists!$B$1:$B$2';

        for ($row = 2; $row <= 200; $row++) {
            $cellC = $sheet->getCell('C'.$row);
            $validationC = $cellC->getDataValidation();
            $validationC->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setErrorTitle('قيمة خاطئة')
                ->setError('القيمة يجب أن تكون ضمن القائمة')
                ->setFormula1('='.$accountTypeRange);

            $cellD = $sheet->getCell('D'.$row);
            $validationD = $cellD->getDataValidation();
            $validationD->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setErrorTitle('قيمة خاطئة')
                ->setError('القيمة يجب أن تكون ضمن القائمة')
                ->setFormula1('='.$typeRange);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    public function dataExel(): array
    {
        return $this->dataExcel();
    }

    public function name()
    {
        return __('accusoft::models/as_tree_account.singular');
    }
}
