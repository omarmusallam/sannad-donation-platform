<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Donation extends Model
{
    protected $fillable = [
        'campaign_id',
        'donor_name',
        'donor_email',
        'is_anonymous',
        'amount',
        'currency',
        'payment_method',
        'status',
        'provider',
        'provider_ref',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'paid_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // ✅ Scopes
    public function scopePaid(Builder $q): Builder
    {
        return $q->where('status', 'paid');
    }

    public function scopePaidDateBetween($q, $from, $to)
    {
        // نعتمد paid_at لو موجودة وإلا created_at
        return $q->whereBetween(DB::raw('COALESCE(paid_at, created_at)'), [$from, $to]);
    }

    // ✅ Display name (بدون كسر)
    public function getDisplayDonorNameAttribute(): string
    {
        if ($this->is_anonymous) return app()->getLocale() === 'en' ? 'Anonymous' : 'مجهول';
        return $this->donor_name ?: (app()->getLocale() === 'en' ? 'Donor' : 'متبرع');
    }

    public function receipt()
    {
        return $this->hasOne(\App\Models\Receipt::class);
    }
}
