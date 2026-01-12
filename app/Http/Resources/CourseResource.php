<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title_en' => $this->name,
            'title_ar' => $this->name_ar,
            'description_en' => $this->description,
            'description_ar' => $this->description_ar,
            'price' => (float) $this->price,
            'is_free' => (bool) $this->is_free,
            'instructor_id' => (string) $this->instructor_id,
            'instructor_name' => $this->instructor ? $this->instructor->name : null,
            'instructor_avatar_url' => $this->instructor && $this->instructor->avatar 
                ? asset('storage/' . $this->instructor->avatar) 
                : null,
            'thumbnail_url' => $this->image ? asset('storage/' . $this->image) : null,
            'category_id' => $this->category_id ? (string) $this->category_id : null,
            'category_name_en' => $this->category ? $this->category->name : null,
            'category_name_ar' => $this->category ? $this->category->name_ar : null,
            'duration' => $this->total_duration_minutes,
            'level' => null, // Level field doesn't exist in courses table
            'language' => $this->default_language,
            'rating' => (float) $this->average_rating,
            'total_ratings' => (int) $this->total_ratings,
            'total_students' => (int) $this->enrolled_students,
            'is_featured' => (bool) $this->is_featured,
            'status' => $this->status,
            'sections' => SectionResource::collection($this->whenLoaded('sections')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
