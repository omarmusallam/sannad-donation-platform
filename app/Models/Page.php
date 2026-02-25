<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'meta_title_ar',
        'meta_title_en',
        'meta_description_ar',
        'meta_description_en',
        'is_public',
        'sort_order',
    ];

    public function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        return $locale === 'ar'
            ? ($this->title_ar ?? $this->title_en ?? '')
            : ($this->title_en ?? $this->title_ar ?? '');
    }

    public function content(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $locale === 'ar'
            ? ($this->content_ar ?? $this->content_en)
            : ($this->content_en ?? $this->content_ar);
    }

    public function metaTitle(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $locale === 'ar'
            ? ($this->meta_title_ar ?? $this->meta_title_en)
            : ($this->meta_title_en ?? $this->meta_title_ar);
    }

    public function metaDescription(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $locale === 'ar'
            ? ($this->meta_description_ar ?? $this->meta_description_en)
            : ($this->meta_description_en ?? $this->meta_description_ar);
    }
}
