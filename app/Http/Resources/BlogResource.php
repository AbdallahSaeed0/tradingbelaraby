<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'title' => $this->title,
            'title_ar' => $this->title_ar ?? $this->title,
            'slug' => $this->slug,
            'slug_ar' => $this->slug_ar ?? $this->slug,
            'description' => $this->description ?? '',
            'description_ar' => $this->description_ar ?? $this->description ?? '',
            'excerpt' => $this->excerpt ?? '',
            'excerpt_ar' => $this->excerpt_ar ?? $this->excerpt ?? '',
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'image_ar_url' => $this->image_ar ? asset('storage/' . $this->image_ar) : null,
            'category_id' => $this->category_id ? (string) $this->category_id : null,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => (string) $this->category->id,
                    'name' => $this->category->name,
                    'name_ar' => $this->category->name_ar ?? $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'author_id' => $this->author_id ? (string) $this->author_id : null,
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => (string) $this->author->id,
                    'name' => $this->author->name,
                    'avatar_url' => $this->author->avatar ? asset('storage/' . $this->author->avatar) : null,
                ];
            }),
            'status' => $this->status,
            'is_featured' => $this->is_featured ?? false,
            'views_count' => $this->views_count ?? 0,
            'reading_time' => $this->reading_time ?? 0,
            'tags' => $this->tags ?? [],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
