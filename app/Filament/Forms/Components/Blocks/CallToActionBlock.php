<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class CallToActionBlock extends Block
{
    public static function make(string $name = 'call_to_action'): static
    {
        return parent::make($name)
            ->label('Call to Action')
            ->schema([
                Forms\Components\TextInput::make('headline')
                    ->label('Headline')
                    ->required(),
                Forms\Components\MarkdownEditor::make('text')
                    ->label('Text Content')
                    ->required(),
                Forms\Components\TextInput::make('button_text')
                    ->label('Button Text')
                    ->required(),
                Forms\Components\TextInput::make('button_url')
                    ->label('Button URL')
                    ->placeholder('/om-kalibr')
                    ->required(),
                Forms\Components\Select::make('text_alignment')
                    ->label('Text Alignment')
                    ->options([
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right',
                    ])
                    ->default('center'),
                Forms\Components\ColorPicker::make('text_color')
                    ->label('Text Color')
                    ->default('#000000'),
                Forms\Components\Select::make('background_type')
                    ->label('Background Type')
                    ->options([
                        'color' => 'Color',
                        'image' => 'Image',
                    ])
                    ->default('color')
                    ->reactive(),
                Forms\Components\ColorPicker::make('background_color')
                    ->label('Background Color')
                    ->visible(fn (Forms\Get $get) => $get('background_type') === 'color')
                    ->default('#ffffff'),
                Forms\Components\FileUpload::make('background_image')
                    ->label('Background Image')
                    ->image()
                    ->disk('public')
                    ->directory('cta-backgrounds')
                    ->visible(fn (Forms\Get $get) => $get('background_type') === 'image'),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
