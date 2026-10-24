<?php

namespace App\Filament\Forms\Components\Blocks;

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
                Textarea::make('column_one')
                    ->required()
                    ->helperText('Paragraphs separated by a blank line. The first one is set larger'),
                Textarea::make('column_two')
                    ->required()
                    ->helperText('Paragraphs separated by a blank line'),
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
            ]);
    }
}
