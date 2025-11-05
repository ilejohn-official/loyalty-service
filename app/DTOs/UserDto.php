<?php

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Simple User Data Transfer Object used across the loyalty service.
 */
class UserDto implements Arrayable
{
  public int|null $id;
  public ?string $name;
  public ?string $email;
  public array $attributes = [];

  public function __construct(array $data = [])
  {
    $this->id = isset($data['id']) ? (int) $data['id'] : null;
    $this->name = $data['name'] ?? null;
    $this->email = $data['email'] ?? null;
    $this->attributes = $data;
  }

  public static function fromModel($model): self
  {
    return new self($model->toArray());
  }

  public function toArray(): array
  {
    return $this->attributes;
  }

  public function __get($key)
  {
    return $this->attributes[$key] ?? null;
  }
}
