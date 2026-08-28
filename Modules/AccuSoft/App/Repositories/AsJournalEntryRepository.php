<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\JournalEntry;
use App\Models\AccuSoft\JournalEntryDetail;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\Branch;
use App\Models\Organization;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class AsJournalEntryRepository extends BaseRepository
{
    protected $fieldSearchable = ['entry_number', 'entry_date', 'description', 'fiscal_year_id', 'entry_type', 'source', 'status', 'total_debit', 'total_credit', 'created_by', 'posted_by', 'posted_at', 'is_locked', 'locked_at', 'locked_by', 'reference_type', 'reference_id'];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit)->with(['creator']);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.JournalEntry';

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

    public function model(): string
    {
        return JournalEntry::class;
    }

    public function statuses(): array
    {
        return JournalEntry::statuseslist();
    }

    public function sources(): array
    {
        return JournalEntry::sources();
    }


    public  function branchs()
    {
        return Branch::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function typesList()
    {
        return JournalEntry::typesList();
    }

    public function types()
    {
        return JournalEntry::types();
    }

    public function TreeAccounts()
    {
        return TreeAccounts::active()
            ->get()
            ->mapWithKeys(function ($account) {
                return [
                    $account->id => $account->name . ' (' . $account->code . ')',
                ];
            })
            ->toArray();
    }

    public function CostCenters()
    {
        return CostCenters::active()
            ->get()
            ->mapWithKeys(function ($account) {
                return [
                    $account->id => $account->name . ' (' . $account->code . ')',
                ];
            })
            ->toArray();
    }
    public function listItems($id)
    {
        return JournalEntry::findOrFail($id);
    }

    public function getHeaders(): array
    {
        return [__('accusoft::models/as_journal_entries.fields.entry_number'), __('accusoft::models/as_journal_entries.fields.entry_date'), __('accusoft::models/as_journal_entries.fields.description'), __('accusoft::models/as_journal_entries.fields.total_debit'), __('accusoft::models/as_journal_entries.fields.total_credit'), __('accusoft::models/as_journal_entries.fields.status'), __('accusoft::models/as_journal_entries.fields.created_at')];
    }

    public function dataExcel(): array
    {
        return JournalEntry::with('creator')
            ->get()
            ->map(function ($entry) {
                return [
                    'entry_number' => $entry->entry_number,
                    'entry_date' => $entry->entry_date ? $entry->entry_date->format('Y-m-d') : '',
                    'description' => $entry->description,
                    'total_debit' => $entry->total_debit,
                    'total_credit' => $entry->total_credit,
                    'status' => $entry->status_text,

                    'created_at' => $entry->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function dataExceldetails($id): array
    {
        return JournalEntryDetail::with(['treeAccount', 'costCenter'])
            ->where('journal_entry_id', $id)
            ->get()
            ->map(function ($detail) {
                return [
                    'account' => $detail->treeAccount ? $detail->treeAccount->name . ' (' . $detail->treeAccount->code . ')' : '',
                    'cost_center' => $detail->costCenter ? $detail->costCenter->name . ' (' . $detail->costCenter->code . ')' : '',
                    'debit' => $detail->debit,
                    'credit' => $detail->credit,
                    'description' => $detail->description,
                ];
            })
            ->toArray();
    }



    public  function getdataorganization( )
    {
       return Organization::first();
    }
    public function getHeadersdetails(): array
    {
        return [__('accusoft::models/as_journal_entries.fields.account_id'), __('accusoft::models/as_journal_entries.fields.cost_center_id'), __('accusoft::models/as_journal_entries.fields.debit'), __('accusoft::models/as_journal_entries.fields.credit'), __('accusoft::models/as_journal_entries.fields.description')];
    }

    public function name()
    {
        return __('accusoft::models/as_journal_entries.plural');
    }
}
