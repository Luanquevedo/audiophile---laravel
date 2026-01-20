<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource
 *
 * Defines the public representation of a User entity exposed by the API.
 * This resource intentionally exposes only non-sensitive fields and
 * provides a stable contract for frontend consumers.
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at?->toISOString(), // ISO-8601 for frontend consistency
        ];
    }
}
