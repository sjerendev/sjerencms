<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;

class RegisterBlock extends Block
{
    public static function make(?string $name = 'register'): static
    {
        return parent::make($name)
            ->label('Project register')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                TextInput::make('aside')
                    ->helperText('{total} becomes the number of projects'),
                ProjectPicker::make()
                    ->helperText('Empty means every project, by position'),
                TextInput::make('all_label')
                    ->helperText('Link to all projects, with {total}. Leave empty for a plain closing line'),
            ]);
    }
}
