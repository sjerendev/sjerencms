<?php

namespace App\Filament\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class HeroSection extends Section
{
    public static function make(Htmlable|\Closure|array|string|null $heading = 'Hero Section'): static
    {
        return parent::make($heading)
            ->schema([
                Forms\Components\Select::make('hero_background_type')
                    ->options([
                        'image' => 'Background Image',
                        'color' => 'Background Color',
                        'both' => 'Image & Color',
                    ])
                    ->live(),

                Forms\Components\FileUpload::make('hero_image')
                    ->image()
                    ->disk('public')
                    ->directory('hero-images')
                    ->visibility('public')
                    ->visible(fn(Forms\Get $get) => in_array($get('hero_background_type'), ['image', 'both'])),

                Forms\Components\ColorPicker::make('hero_background_color')
                    ->visible(fn(Forms\Get $get) => in_array($get('hero_background_type'), ['color', 'both'])),

                Forms\Components\ColorPicker::make('hero_text_color'),

                Forms\Components\TextInput::make('hero_height')
                    ->placeholder('Example: 50svh, 100vh, 500px')
                    ->helperText('Enter a valid CSS height value'),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('hero_headline')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hero_subheadline')
                            ->maxLength(255),
                    ]),

                Forms\Components\Textarea::make('hero_paragraph')
                    ->maxLength(500),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('hero_cta_text')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('hero_cta_url')
                            ->url(),
                    ]),
            ]);
    }
}
