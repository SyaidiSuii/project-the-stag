<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableLayoutSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'container_width',
        'container_height',
    ];

    /**
     * Get or create setting by key
     */
    public static function getOrCreate(string $key, int $defaultWidth = 1200, int $defaultHeight = 600)
    {
        return static::firstOrCreate(
            ['key' => $key],
            [
                'container_width' => $defaultWidth,
                'container_height' => $defaultHeight,
            ]
        );
    }
}
