<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, dynamic>
     */
    public function toArray(Request $request): array
    {
        $items = [];
        if ($this->whenLoaded('items')) {
            foreach ($this->items as $item) {
                $course = $item->course ?? null;
                $items[] = [
                    'course_id' => (string) $item->course_id,
                    'course_title' => $course ? ($course->name ?? '') : '',
                    'course_title_ar' => $course ? ($course->name_ar ?? '') : null,
                    'price' => (float) $item->price,
                    'thumbnail_url' => $course && $course->image ? asset('storage/' . $course->image) : null,
                ];
            }
        }

        return [
            'id' => (string) $this->id,
            'user_id' => (string) $this->user_id,
            'order_number' => $this->order_number,
            'total_amount' => (float) $this->total,
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'tax_amount' => 0.0,
            'payment_method' => $this->payment_method ?? 'cash_on_delivery',
            'payment_status' => $this->status === 'completed' ? 'success' : 'pending',
            'order_status' => $this->status,
            'status' => $this->status,
            'coupon_code' => $this->coupon->code ?? null,
            'items' => $items,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
