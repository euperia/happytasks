<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait UsesUserId
{

    protected static function bootUsesUserId()
    {
        static::creating(function ($model) {
            if (empty($model->user_id) && empty(auth()->user()->id)) {
                throw new \InvalidArgumentException('Missing authenticated user');
            }
            if (empty($model->user_id)) {
                $model->user_id = auth()->user()->id;
            }
        });
    }

}
