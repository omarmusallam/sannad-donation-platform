<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Report extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'period_month',
        'period_year',
        'campaign_id',
        'pdf_path',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'period_month' => 'integer',
        'period_year' => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // ✅ Scopes (احترافية + تسهّل الكنترولرز)
    public function scopePublic(Builder $q): Builder
    {
        return $q->where('is_public', true);
    }

    public function scopeLatestPeriod(Builder $q): Builder
    {
        return $q->orderByDesc('period_year')->orderByDesc('period_month')->latest();
    }

    public function scopeForPeriod(Builder $q, ?int $year, ?int $month): Builder
    {
        return $q
            ->when($year, fn($qq) => $qq->where('period_year', $year))
            ->when($month, fn($qq) => $qq->where('period_month', $month));
    }

    // ✅ Locale-safe title
    public function getTitleAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            return $this->title_en ?: ($this->title_ar ?: '');
        }
        return $this->title_ar ?: ($this->title_en ?: '');
    }

    // ✅ Locale-safe summary
    public function getSummaryAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            return $this->summary_en ?: ($this->summary_ar ?: '');
        }
        return $this->summary_ar ?: ($this->summary_en ?: '');
    }

    // ✅ Safe PDF url (لا يرجع رابط مكسور)
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    // ✅ Period label helper (اختياري ومفيد للفيو)
    public function getPeriodLabelAttribute(): string
    {
        if ($this->period_year && $this->period_month) {
            return sprintf('%04d-%02d', $this->period_year, $this->period_month);
        }
        return app()->getLocale() === 'en' ? 'General' : 'عام';
    }
}
