<?php

namespace App\Models;

use App\Models\Concerns\UsesUserId;
use App\Models\Concerns\UsesUuid;
use App\Models\Relationships\TaskRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, UsesUserId, TaskRelationships;

    protected $fillable = [
        'name',
        'url',
        'description',
        'notes',
        'due_at',
        'duration',
    ];

    protected $casts = [
        'due_date' => 'datetime:Y-m-d h:i:s'
    ];


}
