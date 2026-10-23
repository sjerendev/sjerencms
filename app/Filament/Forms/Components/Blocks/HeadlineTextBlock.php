<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;

class HeadlineTextBlock extends Block
{
    public static function make(?string $name = 'headline_text'): static
    {
        return parent::make($name)
            ->label('Headline and Text')
            ->schema([
                Select::make('container_type')
                    ->label('Container Type')
                    ->options([
                        'container' => 'Container',
                        'container-fluid' => 'Full Width',
                    ])
                    ->default('container')
                    ->required(),
                Grid::make()
                    ->schema([
                        TextInput::make('headline')
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
                TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
