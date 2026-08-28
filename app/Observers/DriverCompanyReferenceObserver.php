<?php

namespace App\Observers;

use App\Models\Vehicles\DriverCompanyReference;

class DriverCompanyReferenceObserver
{
    public function saving(DriverCompanyReference $reference): void
    {
        if ($reference->status === DriverCompanyReference::STATUS_ACTIVE) {
            DriverCompanyReference::query()
                ->where('driver_id', $reference->driver_id)
                ->when($reference->exists, fn ($query) => $query->where('id', '!=', $reference->id))
                ->where('status', DriverCompanyReference::STATUS_ACTIVE)
                ->each(function (DriverCompanyReference $other): void {
                    $other->updateQuietly([
                        'status' => DriverCompanyReference::STATUS_SUSPENDED,
                        'ended_at' => now(),
                    ]);
                });

            if ($reference->started_at === null) {
                $reference->started_at = now();
            }
        }

        if (
            $reference->isDirty('status')
            && $reference->status !== DriverCompanyReference::STATUS_ACTIVE
            && $reference->getOriginal('status') === DriverCompanyReference::STATUS_ACTIVE
            && $reference->ended_at === null
        ) {
            $reference->ended_at = now();
        }
    }
}
