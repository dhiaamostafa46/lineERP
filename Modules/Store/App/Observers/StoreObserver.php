<?php

namespace Modules\Store\App\Observers;

use App\Models\StoreApp\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StoreObserver
{
    public function creating($model): void
    {
        if (! Auth::check()) {
            return;
        }

        $excluded = ['Stock'];

        if (! in_array(class_basename($model), $excluded)) {
            if (in_array('user_id', $model->getFillable())) {
                $model->user_id = Auth::id();
            }
            if (in_array('org_id', $model->getFillable())) {
                if (is_null($model->org_id)) {
                    $model->org_id = Auth::user()->org_id;
                }
            }
        }
    }

    public function saved(Store $store): void
    {
        if (! $store->wasChanged('manager_user_id')) {
            return;
        }

        $previousManagerId = $store->getOriginal('manager_user_id');

        if ($previousManagerId && (int) $previousManagerId !== (int) $store->manager_user_id) {
            $stillManagesAnotherStore = Store::query()
                ->where('manager_user_id', $previousManagerId)
                ->where('id', '!=', $store->id)
                ->exists();

            if (! $stillManagesAnotherStore) {
                User::query()
                    ->where('id', $previousManagerId)
                    ->update(['user_type' => 'admin']);
            }
        }

        if ($store->manager_user_id !== null) {
            User::query()
                ->where('id', $store->manager_user_id)
                ->update(['user_type' => 'supervisor']);
        }
    }
}
