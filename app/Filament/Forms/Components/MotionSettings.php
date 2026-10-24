<?php

namespace App\Filament\Forms\Components;

use App\Support\TextMotion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * The folded "Motion" panel under a text field: saved as motion.{key} in the
 * block's data, read by TextMotion. English pages take the Swedish page's
 * motion (PageMotion), so they only get a note.
 */
class MotionSettings
{
    public static function make(string $key, string $label): Section
    {
        $english = fn ($livewire) => ($livewire->data['language'] ?? 'sv') === 'en';
        $on = fn (Get $get, $livewire) => ! $english($livewire) && (bool) $get('enabled');

        return Section::make("Motion: {$label}")
            ->statePath("motion.{$key}")
            ->collapsible()
            ->collapsed()
            ->compact()
            ->schema([
                Text::make('Motion is set on the Swedish page.')
                    ->visible($english),
                Toggle::make('enabled')
                    ->label('Animate when it scrolls into view')
                    ->live()
                    // Fill the defaults when turned on, so the fields show what will play.
                    ->afterStateUpdated(function (bool $state, Get $get, Set $set) {
                        foreach ($state ? TextMotion::DEFAULTS : [] as $field => $value) {
                            if ($get($field) === null) {
                                $set($field, $value);
                            }
                        }
                    })
                    ->hidden($english),
                Select::make('from')
                    ->options(['bottom' => 'Bottom', 'top' => 'Top', 'left' => 'Left', 'right' => 'Right'])
                    ->default(TextMotion::DEFAULTS['from'])
                    ->selectablePlaceholder(false)
                    ->visible($on),
                TextInput::make('distance')
                    ->numeric()->minValue(0)->maxValue(400)->suffix('px')
                    ->default(TextMotion::DEFAULTS['distance'])
                    ->helperText('Ignored with a mask: the text starts fully behind the edge')
                    ->visible($on),
                Select::make('split')
                    ->options(['none' => 'Whole text', 'lines' => 'Lines', 'words' => 'Words', 'chars' => 'Letters'])
                    ->default(TextMotion::DEFAULTS['split'])
                    ->selectablePlaceholder(false)
                    ->live()
                    ->visible($on),
                TextInput::make('stagger')
                    ->numeric()->minValue(0)->maxValue(1)->step(0.01)->suffix('s')
                    ->default(TextMotion::DEFAULTS['stagger'])
                    ->visible(fn (Get $get, $livewire) => $on($get, $livewire) && ($get('split') ?? 'none') !== 'none'),
                TextInput::make('duration')
                    ->numeric()->minValue(0.1)->maxValue(3)->step(0.05)->suffix('s')
                    ->default(TextMotion::DEFAULTS['duration'])
                    ->visible($on),
                TextInput::make('delay')
                    ->numeric()->minValue(0)->maxValue(5)->step(0.05)->suffix('s')
                    ->default(TextMotion::DEFAULTS['delay'])
                    ->visible($on),
                Select::make('ease')
                    ->options(['soft' => 'Soft', 'sharp' => 'Sharp', 'bouncy' => 'Bouncy'])
                    ->default(TextMotion::DEFAULTS['ease'])
                    ->selectablePlaceholder(false)
                    ->visible($on),
                Toggle::make('mask')
                    ->label('Mask: slide in from behind an edge')
                    ->default(TextMotion::DEFAULTS['mask'])
                    ->visible($on),
            ]);
    }
}
