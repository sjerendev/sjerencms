<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class StackBlock extends Block
{
    public static function make(?string $name = 'stack'): static
    {
        return parent::make($name)
            ->label('Stack')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                MotionSettings::make('head', 'Section head'),
                Repeater::make('groups')
                    ->schema([
                        TextInput::make('label')
                            ->required(),
                        TextInput::make('items')
                            ->required()
                            ->helperText('One line, separated as you want it shown, e.g. Figma · Penpot'),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(1)
                    ->required(),
                MotionSettings::make('groups_label', 'Group labels'),
                MotionSettings::make('groups_items', 'Group items'),
            ]);
    }
}
