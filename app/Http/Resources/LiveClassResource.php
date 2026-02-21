<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $startTime = $this->scheduled_at;
        $endTime = $startTime
            ? $startTime->copy()->addMinutes($this->duration_minutes ?? 60)
            : null;

        return [
            'id' => (string) $this->id,
            'course_id' => (string) $this->course_id,
            'title_en' => $this->name ?? '',
            'title_ar' => $this->name_ar ?? '',
            'description_en' => $this->description ?? '',
            'description_ar' => $this->description_ar ?? '',
            'start_time' => $startTime?->toIso8601String(),
            'end_time' => $endTime?->toIso8601String(),
            'status' => $this->status ?? 'scheduled',
            'platform' => $this->link ? 'zoom' : 'other',
            'meeting_url' => $this->link,
            'meeting_id' => null,
            'meeting_password' => null,
            'instructor_id' => $this->instructor_id ? (string) $this->instructor_id : null,
            'instructor_name' => $this->instructor?->name,
            'recording_url' => $this->recording_url,
            'materials' => collect($this->material_urls ?? [])->map(fn ($url, $idx) => [
                'id' => (string) $idx,
                'name' => basename(($this->materials ?? [])[$idx] ?? 'file'),
                'url' => $url,
                'type' => 'file',
            ])->values()->toArray(),
            'registered_user_ids' => $this->when(
                $request->user(),
                fn () => $this->registrations()->pluck('user_id')->map(fn ($id) => (string) $id)->toArray(),
                []
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
