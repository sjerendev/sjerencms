<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\FileUpload;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class CallToActionBlock extends Block
{
    public static function make(?string $name = 'call_to_action'): static
    {
        return parent::make($name)
            ->label('Call to Action')
            ->schema([
                TextInput::make('headline')
                    ->label('Headline')
                    ->required(),
                MarkdownEditor::make('text')
                    ->label('Text Content')
                    ->required(),
                TextInput::make('button_text')
                    ->label('Button Text')
                    ->required(),
                TextInput::make('button_url')
                    ->label('Button URL')
                    ->placeholder('/om-kalibr')
                    ->required(),
                Select::make('text_alignment')
                    ->label('Text Alignment')
                    ->options([
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right',
                    ])
                    ->default('center'),
                ColorPicker::make('text_color')
                    ->label('Text Color')
                    ->default('#000000'),
                Select::make('background_type')
                    ->label('Background Type')
                    ->options([
                        'color' => 'Color',
                        'image' => 'Image',
                    ])
                    ->default('color')
                    ->reactive(),
                ColorPicker::make('background_color')
                    ->label('Background Color')
                    ->visible(fn (Get $get) => $get('background_type') === 'color')
                    ->default('#ffffff'),
                FileUpload::make('background_image')
                    ->label('Background Image')
                    ->image()
                    ->disk('public')
                    ->directory('cta-backgrounds')
                    ->visible(fn (Get $get) => $get('background_type') === 'image'),
                TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
