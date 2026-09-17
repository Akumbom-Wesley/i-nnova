<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Enquiries per month over the last year, so a quiet spell or a spike after a
 * campaign is visible rather than something you would have to go counting for.
 */
class EnquiriesChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Enquiries over the past year';

    protected ?string $description = 'Counted by the month they arrived.';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '14rem';

    public function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);

        // Every month in the window, so a month with nothing shows as a gap
        // rather than being dropped and distorting the shape.
        $months = collect(range(0, 11))
            ->mapWithKeys(function (int $offset) use ($start): array {
                $month = $start->copy()->addMonths($offset);

                return [$month->format('Y-m') => 0];
            });

        $counts = Lead::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn (Lead $lead): string => $lead->created_at->format('Y-m'))
            ->map(fn ($group): int => $group->count());

        $series = $months->merge($counts);

        return [
            'datasets' => [
                [
                    'label' => 'Enquiries',
                    'data' => $series->values()->all(),
                    'backgroundColor' => '#1157B6',
                    'borderColor' => '#1157B6',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $series->keys()
                ->map(fn (string $key): string => Carbon::createFromFormat('Y-m', $key)->format('M'))
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                // Whole numbers only: half an enquiry is not a thing.
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
