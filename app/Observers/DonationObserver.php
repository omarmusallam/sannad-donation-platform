<?php

namespace App\Observers;

use App\Models\Donation;
use App\Services\CampaignTotals;

class DonationObserver
{
    public function created(Donation $donation): void
    {
        if ($donation->campaign) {
            CampaignTotals::refresh($donation->campaign);
        }
    }

    public function updated(Donation $donation): void
    {
        if ($donation->wasChanged(['status', 'amount', 'campaign_id'])) {
            if ($donation->campaign) {
                CampaignTotals::refresh($donation->campaign);
            }

            $originalCampaignId = $donation->getOriginal('campaign_id');
            if ($originalCampaignId && $originalCampaignId !== $donation->campaign_id) {
                $originalCampaign = \App\Models\Campaign::find($originalCampaignId);

                if ($originalCampaign) {
                    CampaignTotals::refresh($originalCampaign);
                }
            }
        }
    }

    public function deleted(Donation $donation): void
    {
        if ($donation->campaign) {
            CampaignTotals::refresh($donation->campaign);
        }
    }
}
