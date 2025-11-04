<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyTransactionResource extends JsonResource
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
      'amount' => $this->amount,
      'type' => $this->type,
      'points_earned' => $this->points_earned,
      'user' => new UserResource($this->whenLoaded('user')),
      'created_at' => $this->created_at?->toISOString(),
    ];
  }
}
