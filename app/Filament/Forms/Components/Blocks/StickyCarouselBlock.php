<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class StickyCarouselBlock extends Block
{
    public static function make(?string $name = 'sticky_carousel'): static
    {
        return parent::make($name)
            ->label('Sticky Carousel')
            ->schema([
                TextInput::make('heading')
                    ->label('Section Heading (Optional)')
                    ->placeholder('e.g., Selected Works'),

                TextInput::make('subheading')
                    ->label('Subheading (Optional)'),

                TextInput::make('section_height')
                    ->label('Section Height')
                    ->placeholder('100vh')
                    ->default('100vh')
                    ->helperText('Supports px, %, vh, svh values'),

                Repeater::make('images')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('sticky-carousel')
                            ->visibility('public')
                            ->required(),
                        TextInput::make('title')
                            ->label('Project Title')
                            ->required(),
                        TextInput::make('url')
                            ->label('Link URL')
                            ->url()
                            ->placeholder('https://...')
                            ->required(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->minItems(3),

                TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
