<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ImageTextBlock extends Block
{
    public static function make(string $name = 'image_text'): static
    {
        return parent::make($name)
            ->label('Image & Text')
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('content-images')
                    ->visibility('public'),
                
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\MarkdownEditor::make('text')
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
                            ])
                            ->fileAttachmentsDirectory('content-images'),
                    ]),

                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('button1_text')
                                ->label('First Button Text'),
                            Forms\Components\TextInput::make('button1_url')
                                ->label('First Button URL')
                                ->rule('regex:/^(https?:\/\/.*|\/.*)/'),
                        ]),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('button2_text')
                                ->label('Second Button Text'),
                            Forms\Components\TextInput::make('button2_url')
                                ->label('Second Button URL')
                                ->rule('regex:/^(https?:\/\/.*|\/.*)/'),
                        ]),
                    ]),

                Forms\Components\Toggle::make('reverse_layout')
                    ->label('Reverse Layout')
                    ->default(false),

                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
