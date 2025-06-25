<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Post;
use App\Models\Category;
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

            Stat::make('Total Posts', Post::count())
                ->description('Published and draft posts')
                ->icon('heroicon-o-newspaper'),

            Stat::make('Categories', Category::count())
                ->description('Content categories')
                ->icon('heroicon-o-tag'),
        ];
    }
}
