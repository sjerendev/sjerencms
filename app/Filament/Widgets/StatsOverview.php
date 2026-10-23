<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pages', Page::count())
                ->description('Published and draft pages')
                ->icon('heroicon-o-document'),
        ];
    }
}
