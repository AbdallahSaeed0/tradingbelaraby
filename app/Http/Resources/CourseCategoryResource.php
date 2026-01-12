<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseCategoryResource extends JsonResource
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
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'slug' => $this->slug,
            'description_en' => $this->description,
            'description_ar' => $this->description_ar,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'is_featured' => (bool) $this->is_featured,
            'courses_count' => $this->when(isset($this->courses_count), $this->courses_count),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
