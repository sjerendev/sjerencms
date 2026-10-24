<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
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
                MotionSettings::make('head', 'Section head'),
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
                MotionSettings::make('items_question', 'Questions'),
                MotionSettings::make('items_answer', 'Answers'),
                MotionSettings::make('items_note', 'Notes'),
            ]);
    }
}
