<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;

class FullTextBlock extends Block
{
    public static function make(string $name = 'full_text'): static
    {
        return parent::make($name)
            ->label('Full Width Text')
            ->schema([
                Forms\Components\Grid::make()
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
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
