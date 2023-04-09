<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\UsesUuid;
use App\Models\Concerns\UsesUserId;
use App\Models\Relationships\StatusRelationship;

class Status extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, UsesUserId, StatusRelationship;

    protected $fillable = [
      'name',
      'position'
    ];
}
