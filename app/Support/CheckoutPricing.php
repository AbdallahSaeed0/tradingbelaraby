<?php

namespace App\Support;

class CheckoutPricing
{
    /**
     * Split the post-discount order total across line items proportionally.
     *
     * @param  array<int, array{key: string, price: float}>  $items
     * @return array<string, float>
     */
    public static function allocateLineTotals(array $items, float $subtotal, float $discount): array
    {
        if ($items === []) {
            return [];
        }

        $total = max(0, round($subtotal - $discount, 2));
        $amounts = [];
        $remaining = $total;
        $count = count($items);

        foreach ($items as $index => $item) {
            $price = (float) $item['price'];

            if ($index === $count - 1) {
                $paid = round($remaining, 2);
            } elseif ($subtotal > 0 && $discount > 0) {
                $paid = round($price - ($discount * ($price / $subtotal)), 2);
                $remaining -= $paid;
            } else {
                $paid = round($price, 2);
                $remaining -= $paid;
            }

            $amounts[$item['key']] = $paid;
        }

        return $amounts;
    }

    /**
     * Split a bundle line total across its courses by list price.
     *
     * @param  iterable<int, object{price: float|string}>  $courses
     * @return array<int, float> course_id => paid amount
     */
    public static function splitBundleAmountAmongCourses(iterable $courses, float $bundlePaid): array
    {
        $courses = collect($courses);
        if ($courses->isEmpty()) {
            return [];
        }

        $bundleSubtotal = $courses->sum(fn ($course) => (float) $course->price);
        if ($bundleSubtotal <= 0) {
            $each = round($bundlePaid / $courses->count(), 2);
            return $courses->mapWithKeys(fn ($course) => [$course->id => $each])->all();
        }

        $amounts = [];
        $remaining = $bundlePaid;
        $lastIndex = $courses->count() - 1;

        foreach ($courses->values() as $index => $course) {
            if ($index === $lastIndex) {
                $amounts[$course->id] = round($remaining, 2);
            } else {
                $share = round($bundlePaid * ((float) $course->price / $bundleSubtotal), 2);
                $amounts[$course->id] = $share;
                $remaining -= $share;
            }
        }

        return $amounts;
    }
}
