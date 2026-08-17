<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Campaign;
use Filament\Widgets\ChartWidget;

/**
 * Scoped to the Fulfillment page only (getHeaderWidgets()) — kept out of the main
 * Dashboard's auto-discovered widget list via $isDiscovered = false.
 */
class CampaignProgressChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Campaign Progress (Last 6 Months)';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $created = [];
        $completed = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $labels[] = $monthDate->format('M Y');

            $created[] = Campaign::whereMonth('created_at', $monthDate->month)
                ->whereYear('created_at', $monthDate->year)
                ->count();

            // No dedicated completed_at column — approximated by when a completed
            // campaign was last touched.
            $completed[] = Campaign::where('status', Campaign::STATUS_COMPLETED)
                ->whereMonth('updated_at', $monthDate->month)
                ->whereYear('updated_at', $monthDate->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Created',
                    'data'            => $created,
                    'borderColor'     => '#0033e7',
                    'backgroundColor' => '#0033e7',
                    'fill'            => false,
                ],
                [
                    'label'           => 'Completed',
                    'data'            => $completed,
                    'borderColor'     => '#22c55e',
                    'backgroundColor' => '#22c55e',
                    'fill'            => false,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
