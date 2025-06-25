<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class GalleryBlock extends Block
{
    public static function make(string $name = 'gallery'): static
    {
        return parent::make($name)
            ->label('Gallery')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('gallery-images')
                    ->visibility('public'),
            ]);
    }
}
