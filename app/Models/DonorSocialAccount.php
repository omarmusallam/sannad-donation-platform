<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonorSocialAccount extends Model
{
    protected $fillable = [
        'donor_id',
        'provider',
        'provider_user_id',
        'provider_email',
        'provider_name',
        'avatar',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
