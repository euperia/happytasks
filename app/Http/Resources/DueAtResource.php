<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DueAtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->due->format('Y-m-d'),
            'time' => $this->due->format('H:i:s'),
            'timestamp' => $this->due->timestamp,
            'iso8601' => $this->due->toIso8601String(),
            'human_readable' => $this->due->toRfc2822String(),
            'diff' => $this->due->diffForHumans()
        ];
    }
}
