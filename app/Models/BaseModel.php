<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                if (Schema::hasColumn($model->getTable(), 'created_by')) {
                    $model->created_by = Auth::id();
                }

                if (Schema::hasColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
