<?php

namespace App\Models\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait TaskScope
{

    public function scopeDueOn(Builder $query, $date): Builder
    {
        return $query->where('due_at', '=', Carbon::parse($date));
    }
    public function scopeDueBefore(Builder $query, $date): Builder
    {
        $date = Carbon::createFromTimestamp($date);
        return $query->where('due_at', '<=', $date);
    }

    public function scopeDueAfter(Builder $query, $date): Builder
    {
        // date is a timestamp - must convert it to a date
        $date = Carbon::createFromTimestamp($date);
        return $query->where('due_at', '>=', $date);
    }
}
