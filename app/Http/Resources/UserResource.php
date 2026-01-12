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
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'phone' => $this->phone ?? null,
            'phone_number' => $this->phone ?? null,
            'gender' => $this->gender ?? null,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'country' => $this->country ?? null,
            'bio' => $this->bio ?? null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
