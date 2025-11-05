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
      $achievementsCount = $this->resource['achievements_count'] ?? $this->resource->achievements_count ?? null;
      $badgesCount = $this->resource['badges_count'] ?? $this->resource->badges_count ?? null;
    }

    $createdAt = $this->created_at ?? ($this->resource['created_at'] ?? null);

    return [
      'id' => $this->id,
      'name' => $this->name,
      'email' => $this->email,
      'achievements_count' => $achievementsCount,
      'badges_count' => $badgesCount,
      'created_at' => $createdAt?->toISOString() ?? $createdAt,
    ];
  }
}
