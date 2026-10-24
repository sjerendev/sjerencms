<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FaqAccordionBlock extends Block
{
    public static function make(?string $name = 'faq_accordion'): static
    {
        return parent::make($name)
            ->label('FAQ Accordion')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                Repeater::make('items')
                    ->schema([
                        TextInput::make('question')
                            ->required(),
                        Textarea::make('answer')
                            ->required(),
                        Textarea::make('note')
                            ->required(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(1)
                    ->required(),
            ]);
    }
}
