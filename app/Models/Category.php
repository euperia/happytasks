<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UsesUuid;
use App\Models\Concerns\UsesUserId;

class Category extends Model
{
    use HasFactory, UsesUuid, UsesUserId;

    protected $fillable = [
        'name',
        'description',
        'position'
    ];

}
