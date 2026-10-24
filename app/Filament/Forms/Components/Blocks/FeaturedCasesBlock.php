<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;

class FeaturedCasesBlock extends Block
{
    public static function make(?string $name = 'featured_cases'): static
    {
        return parent::make($name)
            ->label('Featured cases')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('aside')
                    ->helperText('{total} becomes the number of projects'),
                MotionSettings::make('head', 'Section head'),
                ProjectPicker::make()
                    ->helperText('Empty hides the section. A project with a full-page screenshot is pinned and scrolls inside its frame'),
            ]);
    }
}
