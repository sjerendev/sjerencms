<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class StickyCarouselBlock extends Block
{
    public static function make(string $name = 'sticky_carousel'): static
    {
        return parent::make($name)
            ->label('Sticky Carousel')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label('Section Heading (Optional)')
                    ->placeholder('e.g., Selected Works'),

                Forms\Components\TextInput::make('subheading')
                    ->label('Subheading (Optional)'),

                Forms\Components\TextInput::make('section_height')
                    ->label('Section Height')
                    ->placeholder('100vh')
                    ->default('100vh')
                    ->helperText('Supports px, %, vh, svh values'),

                Forms\Components\Repeater::make('images')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('sticky-carousel')
                            ->visibility('public')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Project Title')
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->label('Link URL')
                            ->url()
                            ->placeholder('https://...')
                            ->required(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->minItems(3),

                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
