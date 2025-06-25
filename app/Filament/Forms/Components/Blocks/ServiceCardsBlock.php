<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;

class ServiceCardsBlock extends Block
{
    public static function make(string $name = 'service_cards'): static
    {
        return parent::make($name)
            ->label('Service Cards')
            ->schema([
                Forms\Components\TextInput::make('section_title')
                    ->label('Section Title')
                    ->required(),
                Forms\Components\Repeater::make('cards')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->helperText('Enter the icon name (e.g., "ph:calendar-check", "ph:gear", "ph:lightbulb"). View all icons at https://icon-sets.iconify.design/ph/')
                            ->required()
                            ->prefix('ph:')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('browse')
                                    ->label('Browse Icons')
                                    ->url('https://icon-sets.iconify.design/ph/', true)
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                            ),
                        Forms\Components\ColorPicker::make('icon_color')
                            ->label('Icon Color')
                            ->default('#F87171'),
                        Forms\Components\TextInput::make('title')
                            ->label('Service Title')
                            ->required(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                MarkdownEditor::make('description')
                                    ->label('Service Description')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ]),
                            ]),
                    ])
                    ->collapsible()
                    ->reorderable(),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}