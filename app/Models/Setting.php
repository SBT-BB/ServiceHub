<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    protected static $cachedSettings = null;

    public static function get(string $key, $default = null)
    {
        if (self::$cachedSettings === null) {
            self::$cachedSettings = static::pluck('value', 'key')->toArray();
        }

        return array_key_exists($key, self::$cachedSettings) ? self::$cachedSettings[$key] : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        if (self::$cachedSettings !== null) {
            self::$cachedSettings[$key] = $value;
        }
    }
}

