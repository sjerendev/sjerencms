<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ServiceCardsBlock extends Block
{
    public static function make(?string $name = 'service_cards'): static
    {
        return parent::make($name)
            ->label('Service Cards')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                MotionSettings::make('head', 'Section head'),
                Repeater::make('columns')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('items')
                            ->required()
                            ->helperText('One item per line'),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(1)
                    ->required(),
                MotionSettings::make('columns_title', 'Column titles'),
                MotionSettings::make('columns_items', 'Column items'),
            ]);
    }
}
