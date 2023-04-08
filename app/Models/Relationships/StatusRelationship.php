<?php

namespace App\Models\Relationships;

use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait StatusRelationship
{
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
