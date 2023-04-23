<?php

namespace App\Models\Concerns;

trait UsesPosition
{

    protected static function bootUsesPosition()
    {
        static::creating(function ($model) {
            if (empty($model->position)) {
                $model->position = self::getLatestPosition() + 1;
            }
        });
    }

    public static function getLatestPosition(): int
    {
        return self::where('user_id', auth()->user()->id)->max('position') ?? 0;
    }

}
