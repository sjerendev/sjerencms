<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TickerBlock extends Block
{
    public static function make(string $name = 'ticker'): static
    {
        return parent::make($name)
            ->label('Ticker')
            ->schema([
                Forms\Components\ColorPicker::make('background_color')
                    ->label('Background Color')
                    ->default('#ffffff'),
                Forms\Components\ColorPicker::make('text_color')
                    ->label('Text Color')
                    ->default('#000000'),
                Forms\Components\TextInput::make('angle')
                    ->label('Angle')
                    ->numeric()
                    ->default(0)
                    ->placeholder('Enter angle of rotation'),
                Forms\Components\Select::make('direction')
                    ->label('Direction')
                    ->options([
                        'left-to-right' => 'Left to Right',
                        'right-to-left' => 'Right to Left',
                    ])
                    ->default('left-to-right'),
                Forms\Components\TagsInput::make('words')
                    ->label('Words')
                    ->placeholder('Enter words for the ticker'),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
