<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
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
            'name' => $this->name,
            'name_ar' => $this->name_ar ?? $this->name,
            'email' => $this->email,
            'bio' => $this->bio ?? '',
            'bio_ar' => $this->bio_ar ?? $this->bio ?? '',
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'courses_count' => $this->courses_count ?? $this->courses()->count(),
            'total_students' => $this->whenLoaded('courses', function () {
                return $this->courses->sum('enrolled_students');
            }, 0),
            'average_rating' => $this->whenLoaded('courses', function () {
                $avgRating = $this->courses->avg('average_rating');
                return $avgRating ? round($avgRating, 1) : 0;
            }, 0),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
