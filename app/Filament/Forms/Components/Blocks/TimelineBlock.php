<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TimelineBlock extends Block
{
    public static function make(?string $name = 'timeline'): static
    {
        return parent::make($name)
            ->label('Timeline')
            ->schema([
                TextInput::make('number')
                    ->required(),
                TextInput::make('label')
                    ->required(),
                MotionSettings::make('head', 'Section head'),
                TextInput::make('work_label')
                    ->required()
                    ->helperText('Legend and entry label for the work track'),
                TextInput::make('sound_label')
                    ->required()
                    ->helperText('Legend and entry label for the sound track'),
                Repeater::make('entries')
                    ->schema([
                        TextInput::make('years')
                            ->required(),
                        TextInput::make('title')
                            ->required(),
                        TextInput::make('subtitle')
                            ->required(),
                        Textarea::make('body')
                            ->required(),
                        TagsInput::make('tags'),
                        Select::make('track')
                            ->options(['work' => 'Work', 'sound' => 'Sound'])
                            ->default('work')
                            ->required(),
                    ])
                    ->collapsible()
                    ->reorderable()
                    ->defaultItems(1)
                    ->required(),
                MotionSettings::make('entries_years', 'Years'),
                MotionSettings::make('entries_title', 'Titles'),
                MotionSettings::make('entries_subtitle', 'Subtitles'),
                MotionSettings::make('entries_body', 'Bodies'),
            ]);
    }
}
