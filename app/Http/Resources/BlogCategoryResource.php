<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
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
            'name_ar' => $this->name_ar ?? $this->name,
            'slug' => $this->slug,
            'description' => $this->description ?? '',
            'description_ar' => $this->description_ar ?? $this->description ?? '',
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'blogs_count' => $this->whenCounted('blogs', function () {
                return $this->blogs_count;
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
