<?php

namespace App\Observers;

use App\Models\Donation;
use App\Services\CampaignTotals;

class DonationObserver
{
    public function created(Donation $donation): void
    {
        CampaignTotals::refresh($donation->campaign);
    }

    public function updated(Donation $donation): void
    {
        if ($donation->wasChanged(['status', 'amount', 'campaign_id'])) {
            CampaignTotals::refresh($donation->campaign);
        }
    }

    public function deleted(Donation $donation): void
    {
        CampaignTotals::refresh($donation->campaign);
    }
}
