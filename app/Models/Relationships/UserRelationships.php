<?php

namespace App\Models\Relationships;

use App\Models\Category;
use App\Models\Status;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait UserRelationships
{

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('position');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class)->orderBy('position');
    }

}
