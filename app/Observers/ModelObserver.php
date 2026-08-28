<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class ModelObserver
{
    public function creating($model)
    {
        if (!Auth::check()) return;



        // الموديولات المستبعدة
        $excluded = ['Stock']; // يمكن إضافة أي موديل آخر هنا

        if (!in_array(class_basename($model), $excluded)) {
            if (in_array('user_id', $model->getFillable())) {
                // Set user_id only if it's not already set
                $model->user_id =  Auth::id();
            }
            if (in_array('org_id', $model->getFillable())) {
                // Set org_id from the authenticated user only if it's not already set on the model
                if (is_null($model->org_id)) {
                    $model->org_id = Auth::user()->org_id;
                }
            }
        }
    }
}
