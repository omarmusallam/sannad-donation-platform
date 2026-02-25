<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Campaign extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'slug',
        'description_ar',
        'description_en',
        'goal_amount',
        'current_amount',
        'currency',
        'status',
        'is_featured',
        'priority',
        'cover_image_path',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'is_featured' => 'boolean',
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->title_en ?? $campaign->title_ar);
            }
        });
    }

    // رابط الغلاف
    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image_path ? asset('storage/' . $this->cover_image_path) : null;
    }

    // توليد slug
    public static function makeSlug(string $title): string
    {
        return Str::slug($title);
    }

    public function donations()
    {
        return $this->hasMany(\App\Models\Donation::class);
    }
    
    public function updates()
    {
        return $this->hasMany(\App\Models\CampaignUpdate::class);
    }

    // مجموع المدفوع فقط
    public function getPaidTotalAttribute(): float
    {
        return (float) $this->donations()
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'en'
            ? ($this->title_en ?: $this->title_ar)
            : $this->title_ar;
    }

    public function getDescriptionAttribute(): string
    {
        return app()->getLocale() === 'en'
            ? ($this->description_en ?: $this->description_ar)
            : $this->description_ar;
    }

    public function getProgressPercentAttribute(): int
    {
        if ((float)$this->goal_amount <= 0) return 0;
        $p = ((float)$this->current_amount / (float)$this->goal_amount) * 100;
        return (int) max(0, min(100, round($p)));
    }
}
