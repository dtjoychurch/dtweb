<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'message_template',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    public static function isEnabled(string $key): bool
    {
        return (bool) static::where('key', $key)->value('is_enabled');
    }

    /**
     * @param  array<string, string>  $replacements  e.g. [':user' => '陳牧師']
     */
    public static function render(string $key, array $replacements): ?string
    {
        $template = static::where('key', $key)->value('message_template');

        if (! $template) {
            return null;
        }

        return strtr($template, $replacements);
    }
}
