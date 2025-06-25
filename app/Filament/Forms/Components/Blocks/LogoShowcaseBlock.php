<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class LogoShowcaseBlock extends Block
{
    public static function make(string $name = 'logo_showcase'): static
    {
        return parent::make($name)
            ->label('Logo Showcase')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label('Heading'),
                Forms\Components\TextInput::make('subheading')
                    ->label('Subheading'),
                Forms\Components\MarkdownEditor::make('section_description')
                    ->label('Section Description'),
                Forms\Components\TextInput::make('logo_height')
                    ->label('Logo Height (px)')
                    ->numeric()
                    ->default(50)
                    ->required(),
                Forms\Components\Repeater::make('logos')
                    ->label('Logos')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Company Name')
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Logo Image')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->label('Website URL')
                            ->url()
                            ->placeholder('/om-kalibr or https://external-site.com'),
                    ])
                    ->collapsible()
                    ->defaultItems(1)
                    ->required(),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
