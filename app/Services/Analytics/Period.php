<?php

namespace App\Services\Analytics;

use Carbon\Carbon;
use Illuminate\Http\Request;

/** The date range a report covers: a preset (last 7/28/90 days) or custom dates. */
class Period
{
    public const PRESETS = [7, 28, 90];

    /** GA4 has no data before this date. */
    private const EARLIEST = '2015-08-14';

    private function __construct(
        public readonly Carbon $start,
        public readonly Carbon $end,
        public readonly string $key,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $period = (string) $request->query('period', '28');

        if ($period === 'custom') {
            try {
                $start = Carbon::createFromFormat('Y-m-d', (string) $request->query('start'))->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', (string) $request->query('end'))->startOfDay();
            } catch (\Throwable) {
                return self::preset(28);
            }
            $end = $end->min(Carbon::today());
            $start = $start->max(Carbon::parse(self::EARLIEST))->min($end);
            // Keep reports reasonably sized: at most two years.
            $start = $start->max($end->copy()->subYears(2));

            return new self($start, $end, 'custom');
        }

        return self::preset((int) $period);
    }

    public static function preset(int $days): self
    {
        $days = in_array($days, self::PRESETS, true) ? $days : 28;

        return new self(Carbon::today()->subDays($days - 1), Carbon::today(), (string) $days);
    }

    public function days(): int
    {
        return (int) $this->start->diffInDays($this->end) + 1;
    }

    public function startDate(): string
    {
        return $this->start->format('Y-m-d');
    }

    public function endDate(): string
    {
        return $this->end->format('Y-m-d');
    }

    /** Query-string parameters that reproduce this period in links. */
    public function query(): array
    {
        return $this->key === 'custom'
            ? ['period' => 'custom', 'start' => $this->startDate(), 'end' => $this->endDate()]
            : ['period' => $this->key];
    }

    public function label(): string
    {
        return $this->key === 'custom'
            ? $this->start->format('M d, Y') . ' – ' . $this->end->format('M d, Y')
            : 'Last ' . $this->key . ' days';
    }
}
