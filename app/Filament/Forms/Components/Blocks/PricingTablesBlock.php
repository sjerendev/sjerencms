<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class PricingTablesBlock extends Block
{
    public static function make(string $name = 'pricing_tables'): static
    {
        return parent::make($name)
            ->label('Pricing Tables')
            ->schema([
                Forms\Components\TextInput::make('section_title')
                    ->label('Section Title'),
                Forms\Components\MarkdownEditor::make('section_description')
                    ->label('Section Description'),
                Forms\Components\Repeater::make('pricing_tables')
                    ->label('Pricing Plans')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Plan Name')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->required(),
                        Forms\Components\TextInput::make('billing_period')
                            ->label('Billing Period')
                            ->placeholder('e.g., /month, /year')
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Plan')
                            ->default(false),
                        Forms\Components\ColorPicker::make('accent_color')
                            ->label('Accent Color'),
                        Forms\Components\TextInput::make('button_text')
                            ->label('Button Text')
                            ->required(),
                        Forms\Components\TextInput::make('button_url')
                            ->label('Button URL')
                            ->placeholder('/om-kalibr')
                            ->required(),
                        Forms\Components\Repeater::make('features')
                            ->label('Features')
                            ->schema([
                                Forms\Components\TextInput::make('feature')
                                    ->label('Feature Text')
                                    ->required(),
                                Forms\Components\Toggle::make('is_included')
                                    ->label('Is Included')
                                    ->default(true),
                            ])
                            ->collapsible()
                            ->defaultItems(1),
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
