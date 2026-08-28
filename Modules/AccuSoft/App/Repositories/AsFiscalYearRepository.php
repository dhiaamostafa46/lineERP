<?php

namespace Modules\AccuSoft\App\Repositories;

use App\Models\AccuSoft\FiscalYear;
use App\Repositories\BaseRepository;

class AsFiscalYearRepository extends BaseRepository
{
    protected $fieldSearchable = ['start_date', 'end_date', 'is_current', 'is_closed', 'notes'];

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $permissionPrefix = 'accusoft.FiscalYear';

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
        return FiscalYear::class;
    }

    /**
     * Get all available statuses
     */
    public function statuses()
    {
        return FiscalYear::statuses();
    }

    /**
     * Get all fiscal years for dropdown/select
     */
    public function fiscalYears()
    {
        return FiscalYear::orderBy('start_date', 'desc')->get()->pluck('full_name', 'id')->toArray();
    }

    /**
     * Get current fiscal year
     */
    public function getCurrentFiscalYear()
    {
        return FiscalYear::current()->first();
    }

    /**
     * Get open fiscal years
     */
    public function getOpenFiscalYears()
    {
        return FiscalYear::open()->orderBy('start_date', 'desc')->get();
    }

    /**
     * Close all open fiscal years
     */
    public function closeFiscalYear()
    {
        $openFiscalYears = FiscalYear::open()->get();

        if ($openFiscalYears->isEmpty()) {
            return false;
        }

        foreach ($openFiscalYears as $fiscalYear) {
            $fiscalYear->update([
                'is_closed' => true,
                'is_current' => false,
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);
            $fiscalYear->JournalEntry()->update([
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => auth()->id(),
            ]);
        }

        return true;
    }

    /**
     * Close specific fiscal year
     */
    public function closeSpecificFiscalYear($fiscalYearId)
    {
        $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

        if ($fiscalYear->is_closed) {
            throw new \Exception(__('accusoft::general.fiscal_year_already_closed'));
        }

        return $fiscalYear->update([
            'is_closed' => true,
            'is_current' => false,
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);
    }

    /**
     * Reopen a closed fiscal year
     */
    public function reopenFiscalYear($fiscalYearId)
    {
        $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

        if (!$fiscalYear->is_closed) {
            throw new \Exception(__('accusoft::general.fiscal_year_not_closed'));
        }

        return $fiscalYear->update([
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
        ]);
    }

    /**
     * Set fiscal year as current and unset others
     */
    public function setAsCurrent($fiscalYearId)
    {
        // Remove current flag from all fiscal years
        FiscalYear::where('is_current', true)->update(['is_current' => false]);

        // Set the selected fiscal year as current
        $fiscalYear = FiscalYear::findOrFail($fiscalYearId);

        return $fiscalYear->update(['is_current' => true]);
    }

    /**
     * Get table headers for export
     */
    public function getHeaders(): array
    {
        return [__('accusoft::models/as_fiscal_years.fields.id'), __('accusoft::models/as_fiscal_years.fields.name'), __('accusoft::models/as_fiscal_years.fields.start_date'), __('accusoft::models/as_fiscal_years.fields.end_date'), __('accusoft::models/as_fiscal_years.fields.status'), __('accusoft::models/as_fiscal_years.fields.closed_at'), __('accusoft::models/as_fiscal_years.fields.closed_by'), __('accusoft::models/as_fiscal_years.fields.created_at')];
    }

    /**
     * Get data for Excel export
     */
    public function dataExcel(): array
    {
        return FiscalYear::with('closedBy')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($fiscalYear) {
                return [
                    'id' => $fiscalYear->id,
                    'name' => $fiscalYear->full_name,
                    'start_date' => $fiscalYear->start_date->format('Y-m-d'),
                    'end_date' => $fiscalYear->end_date->format('Y-m-d'),
                    'status' => $fiscalYear->status_text,
                    'closed_at' => $fiscalYear->closed_at ? $fiscalYear->closed_at->format('Y-m-d H:i') : '-',
                    'closed_by' => $fiscalYear->closedBy ? $fiscalYear->closedBy->name : '-',
                    'created_at' => $fiscalYear->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    /**
     * Get model name for messages
     */
    public function name()
    {
        return __('accusoft::models/as_fiscal_years.singular');
    }

    /**
     * Check if date is within fiscal year range
     */
    public function checkDateInFiscalYear($date, $fiscalYearId = null)
    {
        $query = FiscalYear::whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->where('is_closed', false);

        if ($fiscalYearId) {
            $query->where('id', $fiscalYearId);
        }

        return $query->first();
    }

    /**
     * Get fiscal year statistics
     */
    public function getStatistics()
    {
        return [
            'total' => FiscalYear::count(),
            'open' => FiscalYear::open()->count(),
            'closed' => FiscalYear::closed()->count(),
            'current' => FiscalYear::current()->first(),
        ];
    }
}
