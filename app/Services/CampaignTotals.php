<?php

namespace App\Services;

use App\Models\Campaign;

class CampaignTotals
{
    public static function refresh(Campaign $campaign): void
    {
        $paidTotal = (float) $campaign->donations()
            ->where('status', 'paid')
            ->sum('amount');

        $campaign->update([
            'current_amount' => $paidTotal,
        ]);
    }
}
