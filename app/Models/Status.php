<?php

namespace App\Models;

use App\Models\Concerns\UsesUserId;
use App\Models\Concerns\UsesUuid;
use App\Models\Relationships\StatusRelationship;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, UsesUserId, StatusRelationship;

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }

    protected $fillable = [
      'name',
      'position'
    ];
}
