<?php

namespace App\Filament\Forms\Components\Blocks;

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
            ]);
    }
}
