<?php

namespace App\Observers;

use App\Models\Locality;

class LocalityObserver
{
    public function updated(Locality $locality)
    {
        if ($locality->wasChanged(['membership_id', 'membership_assigned_at'])) {
            if ($locality->membership && $locality->membership_assigned_at) {
                $locality->generateMembershipToken();
            }
        }
    }

    public function created(Locality $locality)
    {
        if ($locality->membership && $locality->membership_assigned_at) {
            $locality->generateMembershipToken();
        }
    }
}
