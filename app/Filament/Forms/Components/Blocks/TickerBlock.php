<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TickerBlock extends Block
{
    public static function make(?string $name = 'ticker'): static
    {
        return parent::make($name)
            ->label('Ticker')
            ->schema([
                ColorPicker::make('background_color')
                    ->label('Background Color')
                    ->default('#ffffff'),
                ColorPicker::make('text_color')
                    ->label('Text Color')
                    ->default('#000000'),
                TextInput::make('angle')
                    ->label('Angle')
                    ->numeric()
                    ->default(0)
                    ->placeholder('Enter angle of rotation'),
                Select::make('direction')
                    ->label('Direction')
                    ->options([
                        'left-to-right' => 'Left to Right',
                        'right-to-left' => 'Right to Left',
                    ])
                    ->default('left-to-right'),
                TagsInput::make('words')
                    ->label('Words')
                    ->placeholder('Enter words for the ticker'),
                TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
