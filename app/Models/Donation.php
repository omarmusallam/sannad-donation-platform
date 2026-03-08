<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Donation extends Model
{
    protected $fillable = [
        'campaign_id',
        'donor_id',        // ✅ NEW
        'donor_name',
        'donor_email',
        'is_anonymous',

        'amount',
        'fees',
        'net_amount',
        'currency',

        'payment_method',
        'status',
        'provider',
        'provider_ref',

        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopePaid(Builder $q): Builder
    {
        return $q->where('status', 'paid');
    }

    public function scopePaidDateBetween(Builder $q, $from, $to): Builder
    {
        // prefer paid_at; fallback created_at
        return $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$from, $to]);
    }

    public function scopeForDonor(Builder $q, int $donorId): Builder
    {
        return $q->where('donor_id', $donorId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getDisplayDonorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return app()->getLocale() === 'en' ? 'Anonymous' : 'مجهول';
        }

        // snapshot first
        if (!empty($this->donor_name)) {
            return $this->donor_name;
        }

        // fallback to donor account
        if ($this->relationLoaded('donor') ? $this->donor : $this->donor()->exists()) {
            return $this->donor?->name ?: (app()->getLocale() === 'en' ? 'Donor' : 'متبرع');
        }

        return app()->getLocale() === 'en' ? 'Donor' : 'متبرع';
    }

    public function getDisplayDonorEmailAttribute(): ?string
    {
        if ($this->is_anonymous) return null;

        return $this->donor_email ?: $this->donor?->email;
    }
}
