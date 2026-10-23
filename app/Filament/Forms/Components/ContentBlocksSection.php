<?php

namespace App\Filament\Forms\Components;

use App\Filament\Forms\Components\Blocks\CallToActionBlock;
use App\Filament\Forms\Components\Blocks\FaqAccordionBlock;
use App\Filament\Forms\Components\Blocks\FullTextBlock;
use App\Filament\Forms\Components\Blocks\HeadlineTextBlock;
use App\Filament\Forms\Components\Blocks\ImageTextBlock;
use App\Filament\Forms\Components\Blocks\StickyCarouselBlock;
use App\Filament\Forms\Components\Blocks\ServiceCardsBlock;
use App\Filament\Forms\Components\Blocks\TickerBlock;
use App\Filament\Forms\Components\Blocks\StaggerTextBlock;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class ContentBlocksSection extends Section
{
    public static function make(Htmlable|Closure|array|string|null $heading = 'Content Blocks'): static
    {
        return parent::make($heading)
            ->schema([
                Forms\Components\Builder::make('content')
                    ->blocks([
                        ImageTextBlock::make(),
                        FullTextBlock::make(),
                        HeadlineTextBlock::make(),
                        StickyCarouselBlock::make(),
                        CallToActionBlock::make(),
                        FaqAccordionBlock::make(),
                        ServiceCardsBlock::make(),
                        TickerBlock::make(),
                        StaggerTextBlock::make(),
                    ])
                    ->collapsible(),
            ]);
    }
}
