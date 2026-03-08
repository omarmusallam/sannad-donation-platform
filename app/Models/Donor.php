<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Donor extends Authenticatable
{
    use Notifiable;

    protected $guard = 'donor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(DonorSocialAccount::class);
    }
}
