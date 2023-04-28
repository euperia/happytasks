<?php

namespace App\Models;

use App\Models\Concerns\UsesUserId;
use App\Models\Concerns\UsesUuid;
use App\Models\Relationships\TaskRelationships;
use App\Models\Scopes\TaskScope;
use App\Models\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes, UsesUuid, UsesUserId, TaskRelationships, TaskScope;

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }
    protected $fillable = [
        'name',
        'url',
        'description',
        'notes',
        'due_at',
        'duration'
    ];

    //    protected $dates = [
    //        'due_at'
    //    ];

    //    protected $casts = [
    //        'due_at' => 'datetime'
    //    ];


    public function getDueAttribute(): Carbon
    {
        return Carbon::create($this->due_at);
    }


}
