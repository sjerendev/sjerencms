<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
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
                MotionSettings::make('head', 'Section head'),
                TextInput::make('headline')
                    ->required(),
                MotionSettings::make('headline', 'Headline'),
                Textarea::make('text')
                    ->required(),
                MotionSettings::make('text', 'Text'),
                Repeater::make('cards')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('body'),
                    ])
                    ->collapsible()
                    ->reorderable(),
                MotionSettings::make('cards_title', 'Card titles'),
                MotionSettings::make('cards_body', 'Card bodies'),
            ]);
    }
}
