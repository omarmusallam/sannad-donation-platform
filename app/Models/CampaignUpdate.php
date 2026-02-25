<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignUpdate extends Model
{
    protected $fillable = [
        'campaign_id',
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'is_public',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'en'
            ? ($this->title_en ?: $this->title_ar)
            : $this->title_ar;
    }

    public function getBodyAttribute(): string
    {
        return app()->getLocale() === 'en'
            ? ($this->body_en ?: $this->body_ar ?: '')
            : ($this->body_ar ?: '');
    }
}
