<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'app.settings.all';

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::query()->get()->keyBy('key')->map(function ($row) {
                return match ($row->type) {
                    'boolean' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $row->value,
                    'json'    => json_decode($row->value ?: '[]', true) ?: [],
                    default   => $row->value,
                };
            })->toArray();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function set(string $key, mixed $value, string $group = 'site', string $type = 'string'): void
    {
        if ($type === 'json') $value = json_encode($value, JSON_UNESCAPED_UNICODE);

        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public function setMany(array $items, string $group): void
    {
        foreach ($items as $key => $meta) {
            $this->set($key, $meta['value'], $group, $meta['type'] ?? 'string');
        }
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
