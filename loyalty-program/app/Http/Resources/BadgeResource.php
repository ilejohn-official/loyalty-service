<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'badge_type' => $this->badge_type?->value,
            'level' => $this->level,
            'earned_at' => $this->earned_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
