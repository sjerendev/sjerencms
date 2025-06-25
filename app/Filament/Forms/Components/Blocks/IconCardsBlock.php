<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;

class IconCardsBlock extends Block
{
    public static function make(string $name = 'icon_cards'): static
    {
        return parent::make($name)
            ->label('Icon Cards')
            ->schema([
                Forms\Components\TextInput::make('section_title')
                    ->label('Section Title')
                    ->required(),
                Forms\Components\Select::make('columns')
                    ->label('Number of Columns')
                    ->options([
                        2 => '2 Columns',
                        3 => '3 Columns',
                        4 => '4 Columns',
                        5 => '5 Columns',
                    ])
                    ->default(3)
                    ->required(),
                Forms\Components\Repeater::make('cards')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Name')
                            ->helperText('Enter the icon name (e.g., "ph:robot", "ph:code", "ph:chart-bar"). View all icons at https://icon-sets.iconify.design/ph/')
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
                            ->default('#000000'),
                        Forms\Components\TextInput::make('title')
                            ->label('Card Title')
                            ->required(),
                        Forms\Components\TextInput::make('link_url')
                            ->label('Read More Link')
                            ->helperText('Enter a URL (e.g., "/utveckling") or anchor (e.g., "#services")')
                            ->placeholder('/page-url or #section-id'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                MarkdownEditor::make('text')
                                    ->required()
                                    ->toolbarButtons([
                                        'heading',
                                        'bold',
                                        'italic',
                                        'strike',
                                        'link',
                                        'blockquote',
                                        'bulletList',
                                        'orderedList',
                                        'codeBlock',
                                        'table',
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
