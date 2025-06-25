<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FullImageBlock extends Block
{
    public static function make(string $name = 'full_image'): static
    {
        return parent::make($name)
            ->label('Full Width Image')
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('content-images')
                    ->visibility('public')
                    ->required(),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
