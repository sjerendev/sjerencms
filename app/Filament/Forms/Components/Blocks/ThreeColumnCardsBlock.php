<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;

class ThreeColumnCardsBlock extends Block
{
    public static function make(string $name = 'three_column_cards'): static
    {
        return parent::make($name)
            ->label('Three Column Cards')
            ->schema([
                Forms\Components\Select::make('columns')
                    ->label('Number of Columns')
                    ->options([
                        3 => '3 Columns',
                        4 => '4 Columns',
                        5 => '5 Columns',
                    ])
                    ->default(3)
                    ->required(),
                Forms\Components\Repeater::make('cards')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('card-images')
                            ->visibility('public'),
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
