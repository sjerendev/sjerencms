<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Builder;
use App\Filament\Forms\Components\Blocks\CallToActionBlock;
use App\Filament\Forms\Components\Blocks\FaqAccordionBlock;
use App\Filament\Forms\Components\Blocks\FullTextBlock;
use App\Filament\Forms\Components\Blocks\HeadlineTextBlock;
use App\Filament\Forms\Components\Blocks\ImageTextBlock;
use App\Filament\Forms\Components\Blocks\StickyCarouselBlock;
use App\Filament\Forms\Components\Blocks\ServiceCardsBlock;
use App\Filament\Forms\Components\Blocks\TickerBlock;
use App\Filament\Forms\Components\Blocks\StaggerTextBlock;
use App\Filament\Forms\Components\Blocks\ColophonBlock;
use App\Filament\Forms\Components\Blocks\RegisterBlock;
use Filament\Forms;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class ContentBlocksSection extends Section
{
    public static function make(Htmlable|Closure|array|string|null $heading = 'Content Blocks'): static
    {
        return parent::make($heading)
            ->schema([
                Builder::make('content')
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
                        ColophonBlock::make(),
                        RegisterBlock::make(),
                    ])
                    ->collapsible(),
            ]);
    }
}
