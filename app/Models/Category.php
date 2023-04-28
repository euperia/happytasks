<?php

namespace App\Models;

use App\Models\Concerns\UsesPosition;
use App\Models\Concerns\UsesUserId;
use App\Models\Concerns\UsesUuid;
use App\Models\Relationships\CategoryRelationships;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, UsesUserId, UsesPosition, CategoryRelationships;

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }
    protected $fillable = [
        'name',
        'description',
        'position',
        'parent_id'
    ];

}
