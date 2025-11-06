<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Support both Eloquent models and lightweight DTOs
        $achievementsCount = null;
        $badgesCount = null;

        if (method_exists($this->resource, 'whenCounted')) {
            $achievementsCount = $this->whenCounted('achievements');
            $badgesCount = $this->whenCounted('badges');
        } else {
            $achievementsCount = property_exists($this->resource, 'achievements_count') ? $this->resource->achievements_count : null;
            $badgesCount = property_exists($this->resource, 'badges_count') ? $this->resource->badges_count : null;
        }

        $createdAt = property_exists($this->resource, 'created_at') ? $this->resource->created_at : null;

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'achievements_count' => $achievementsCount,
            'badges_count' => $badgesCount,
            'created_at' => $createdAt?->toISOString() ?? $createdAt,
        ];
    }
}
