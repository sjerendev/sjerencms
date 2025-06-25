<?php

namespace App\Filament\Forms\Components;

use App\Filament\Forms\Components\Blocks\CallToActionBlock;
use App\Filament\Forms\Components\Blocks\CarouselBlock;
use App\Filament\Forms\Components\Blocks\ContactFormBlock;
use App\Filament\Forms\Components\Blocks\FaqAccordionBlock;
use App\Filament\Forms\Components\Blocks\FullImageBlock;
use App\Filament\Forms\Components\Blocks\FullTextBlock;
use App\Filament\Forms\Components\Blocks\GalleryBlock;
use App\Filament\Forms\Components\Blocks\HeadlineTextBlock;
use App\Filament\Forms\Components\Blocks\IconCardsBlock;
use App\Filament\Forms\Components\Blocks\ImageTextBlock;
use App\Filament\Forms\Components\Blocks\LogoShowcaseBlock;
use App\Filament\Forms\Components\Blocks\PricingTablesBlock;
use App\Filament\Forms\Components\Blocks\TeamMembersBlock;
use App\Filament\Forms\Components\Blocks\ThreeColumnCardsBlock;
use App\Filament\Forms\Components\Blocks\VideoBlock;
use App\Filament\Forms\Components\Blocks\BlogPostsBlock;
use App\Filament\Forms\Components\Blocks\ServiceCardsBlock;
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
                        GalleryBlock::make(),
                        FullTextBlock::make(),
                        HeadlineTextBlock::make(),
                        FullImageBlock::make(),
                        CarouselBlock::make(),
                        ThreeColumnCardsBlock::make(),
                        IconCardsBlock::make(),
                        CallToActionBlock::make(),
                        ContactFormBlock::make(),
                        FaqAccordionBlock::make(),
                        LogoShowcaseBlock::make(),
                        PricingTablesBlock::make(),
                        TeamMembersBlock::make(),
                        VideoBlock::make(),
                        BlogPostsBlock::make(),
                        ServiceCardsBlock::make()
                    ])
                    ->collapsible(),
            ]);
    }
}
