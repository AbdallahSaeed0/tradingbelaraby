<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LectureResource extends JsonResource
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
            'section_id' => (string) $this->section_id,
            'title_en' => $this->title,
            'title_ar' => $this->title_ar,
            'description_en' => $this->description,
            'description_ar' => $this->description_ar,
            'duration' => (int) ($this->duration_minutes ?? 0),
            'video_url' => $this->video_url,
            'video_provider' => $this->content_type === 'video' ? 'mp4' : null,
            'order' => (int) $this->order,
            'is_preview' => (bool) ($this->is_free ?? false),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
