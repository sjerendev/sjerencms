<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;

class HeadlineTextBlock extends Block
{
    public static function make(string $name = 'headline_text'): static
    {
        return parent::make($name)
            ->label('Headline and Text')
            ->schema([
                Forms\Components\Select::make('container_type')
                    ->label('Container Type')
                    ->options([
                        'container' => 'Container',
                        'container-fluid' => 'Full Width',
                    ])
                    ->default('container')
                    ->required(),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('headline')
                            ->required(),
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
