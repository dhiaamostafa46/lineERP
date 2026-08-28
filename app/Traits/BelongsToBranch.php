<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToBranch(): void
    {
        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $model->branch_id = (auth()->check() ? auth()->user()->branch_id : null) ?: (\App\Models\Branch::value('id') ?? 1);
            }
        });

        static::addGlobalScope('branch_isolation', function (Builder $builder) {

            if (auth()->hasUser() && !auth()->user()->can('global.viewBranches')) {
                $table = $builder->getModel()->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'branch_id')) {
                    $builder->where(function ($query) use ($table) {
                        $query->where($table . '.branch_id', auth()->user()->branch_id)
                              ->orWhereNull($table . '.branch_id');
                    });
                }
            }
        });
    }
}

