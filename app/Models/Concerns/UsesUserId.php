<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait UsesUserId
{

    protected static function bootUsesUserId()
    {
        static::creating(function ($model) {
            if (empty(auth()->user()->id)) {
                throw new \InvalidArgumentException('Missing authenticated user');
            }

            $model->user_id = auth()->user()->id;
        });
    }

}
