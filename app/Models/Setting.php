<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->first();
        if (!$row) return $default;

        return match ($row->type) {
            'boolean' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $row->value,
            'json'    => json_decode($row->value ?? 'null', true),
            default   => $row->value,
        };
    }
}
