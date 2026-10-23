<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class HeroSection extends Section
{
    public static function make(Htmlable|Closure|array|string|null $heading = 'Hero Section'): static
    {
        return parent::make($heading)
            ->schema([
                Select::make('hero_background_type')
                    ->options([
                        'image' => 'Background Image',
                        'color' => 'Background Color',
                        'both' => 'Image & Color',
                    ])
                    ->live(),

                FileUpload::make('hero_image')
                    ->image()
                    ->disk('public')
                    ->directory('hero-images')
                    ->visibility('public')
                    ->visible(fn(Get $get) => in_array($get('hero_background_type'), ['image', 'both'])),

                ColorPicker::make('hero_background_color')
                    ->visible(fn(Get $get) => in_array($get('hero_background_type'), ['color', 'both'])),

                ColorPicker::make('hero_text_color'),

                TextInput::make('hero_height')
                    ->placeholder('Example: 50svh, 100vh, 500px')
                    ->helperText('Enter a valid CSS height value'),

                Grid::make(2)
                    ->schema([
                        TextInput::make('hero_headline')
                            ->maxLength(255),
                        TextInput::make('hero_subheadline')
                            ->maxLength(255),
                    ]),

                Textarea::make('hero_paragraph')
                    ->maxLength(500),

                Grid::make(2)
                    ->schema([
                        TextInput::make('hero_cta_text')
                            ->maxLength(50),
                        TextInput::make('hero_cta_url')
                            ->url(),
                    ]),
            ]);
    }
}
