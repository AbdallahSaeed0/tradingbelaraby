<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'course_id' => (string) $this->course_id,
            'title_en' => $this->title,
            'title_ar' => $this->title_ar,
            'description_en' => $this->description,
            'description_ar' => $this->description_ar,
            'order' => (int) $this->order,
            'is_published' => (bool) $this->is_published,
            'lectures' => LectureResource::collection($this->whenLoaded('lectures')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
