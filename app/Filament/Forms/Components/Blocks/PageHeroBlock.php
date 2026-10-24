<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class PageHeroBlock extends Block
{
    public static function make(?string $name = 'page_hero'): static
    {
        return parent::make($name)
            ->label('Page hero')
            ->schema([
                Textarea::make('lines')
                    ->required()
                    ->helperText('The headline, one line per row. Each row break becomes a line break'),
                MotionSettings::make('lines', 'Lines'),
                Textarea::make('text')
                    ->required()
                    ->helperText('{total} becomes the number of projects'),
                MotionSettings::make('text', 'Text'),
                Select::make('size')
                    ->options(['large' => 'Large (om mig)', 'medium' => 'Medium (projekt)'])
                    ->default('large')
                    ->required(),
            ]);
    }
}
