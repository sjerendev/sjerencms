<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class HeroBlock extends Block
{
    public static function make(?string $name = 'hero'): static
    {
        return parent::make($name)
            ->label('Hero')
            ->schema([
                TextInput::make('lead')
                    ->required(),
                Textarea::make('phrases')
                    ->required()
                    ->helperText('One per line. They roll after the lead, the first one stands still without motion'),
                Textarea::make('intro_one')
                    ->required(),
                Textarea::make('intro_two')
                    ->required(),
                TextInput::make('work_label')
                    ->required()
                    ->helperText('Links to the projekt page in this language'),
                TextInput::make('cv_label')
                    ->required(),
                TextInput::make('cv_url')
                    ->required()
                    ->helperText('E.g. /files/Sjeren-Portfolio-cv-2024.pdf'),
            ]);
    }
}
