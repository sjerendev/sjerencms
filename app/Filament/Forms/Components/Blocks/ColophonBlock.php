<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
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
                MotionSettings::make('label', 'Label'),
                Textarea::make('tools')
                    ->required()
                    ->helperText('Separate tools with ·'),
            ]);
    }
}
