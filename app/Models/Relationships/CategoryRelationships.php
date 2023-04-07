<?php

namespace App\Models\Relationships;

use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CategoryRelationships
{

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

}
