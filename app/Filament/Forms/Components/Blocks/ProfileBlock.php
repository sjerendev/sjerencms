<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ProfileBlock extends Block
{
    public static function make(?string $name = 'profile'): static
    {
        return parent::make($name)
            ->label('Profile')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                MotionSettings::make('head', 'Section head'),
                Textarea::make('column_one')
                    ->required()
                    ->helperText('Paragraphs separated by a blank line. The first one is set larger'),
                MotionSettings::make('column_one', 'Column one'),
                Textarea::make('column_two')
                    ->required()
                    ->helperText('Paragraphs separated by a blank line'),
                MotionSettings::make('column_two', 'Column two'),
                Repeater::make('stats')
                    ->schema([
                        TextInput::make('value')
                            ->required(),
                        TextInput::make('label')
                            ->required(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(0),
                MotionSettings::make('stats_value', 'Stat values'),
                MotionSettings::make('stats_label', 'Stat labels'),
            ]);
    }
}
