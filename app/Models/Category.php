<?php

namespace App\Models;

use App\Models\Concerns\UsesUserId;
use App\Models\Concerns\UsesUuid;
use App\Models\Relationships\CategoryRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, UsesUuid, UsesUserId, CategoryRelationships;

    protected $fillable = [
        'name',
        'description',
        'position'
    ];

}
