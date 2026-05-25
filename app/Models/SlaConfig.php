<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaConfig extends Model
{
    protected $fillable = [
        'category_id',
        'priority',
        'resolution_hours',
        'is_active',
    ];

    protected $casts = [
        'resolution_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function getResolutionHours(int $categoryId, string $priority): ?int
    {
        $config = static::where('category_id', $categoryId)
            ->where('priority', $priority)
            ->where('is_active', true)
            ->first();

        return $config?->resolution_hours;
    }
}
