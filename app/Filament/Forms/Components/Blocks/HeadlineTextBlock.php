<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class HeadlineTextBlock extends Block
{
    public static function make(?string $name = 'headline_text'): static
    {
        return parent::make($name)
            ->label('Headline and Text')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('headline')
                    ->required(),
                Textarea::make('text')
                    ->required(),
                Repeater::make('cards')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('body'),
                    ])
                    ->collapsible()
                    ->reorderable(),
            ]);
    }
}
