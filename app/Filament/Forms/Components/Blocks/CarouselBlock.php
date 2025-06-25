<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class CarouselBlock extends Block
{
    public static function make(string $name = 'carousel'): static
    {
        return parent::make($name)
            ->label('Image Carousel')
            ->schema([
                Forms\Components\FileUpload::make('carousel_images')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('carousel-images')
                    ->visibility('public')
                    ->required()
                    ->reorderable()
                    ->maxFiles(20)
                    ->downloadable()
                    ->extraAttributes([
                        'class' => 'space-y-4',
                    ]),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
