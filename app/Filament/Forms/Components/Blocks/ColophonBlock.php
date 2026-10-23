<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ColophonBlock extends Block
{
    public static function make(?string $name = 'colophon'): static
    {
        return parent::make($name)
            ->label('Colophon')
            ->schema([
                TextInput::make('label')
                    ->required(),
                Textarea::make('tools')
                    ->required()
                    ->helperText('Separate tools with ·'),
            ]);
    }
}
