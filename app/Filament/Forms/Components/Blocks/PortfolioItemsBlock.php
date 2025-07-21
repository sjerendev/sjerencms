<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class PortfolioItemsBlock extends Block
{
    public static function make(string $name = 'portfolio_items'): static
    {
        return parent::make($name)
            ->label('Portfolio Items')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label('Heading'),
                Forms\Components\TextInput::make('subheading')
                    ->label('Subheading'),
                Forms\Components\MarkdownEditor::make('section_description')
                    ->label('Section Description'),
                Forms\Components\Repeater::make('portfolio_items')
                    ->label('Portfolio Items')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Project Name')
                            ->required(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->label('Description (What you have done)')
                            ->required(),
                        Forms\Components\FileUpload::make('preview_image')
                            ->label('Preview Image')
                            ->image()
                            ->disk('public')
                            ->directory('portfolio-images')
                            ->required(),
                        Forms\Components\TextInput::make('website_url')
                            ->label('Client Website URL')
                            ->placeholder('https://example.com or # if site no longer exists')
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if ($value === '#') {
                                        return; // Allow # as valid input
                                    }
                                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                                        $fail('The website URL must be a valid URL or # if the site no longer exists.');
                                    }
                                };
                            })
                            ->required(),
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