<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ImageTextBlock extends Block
{
    public static function make(?string $name = 'image_text'): static
    {
        return parent::make($name)
            ->label('Image & Text')
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('content-images')
                    ->visibility('public'),
                
                Grid::make()
                    ->schema([
                        MarkdownEditor::make('text')
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

                Grid::make()
                    ->schema([
                        Group::make([
                            TextInput::make('button1_text')
                                ->label('First Button Text'),
                            TextInput::make('button1_url')
                                ->label('First Button URL')
                                ->rule('regex:/^(https?:\/\/.*|\/.*|#.*)/'),
                        ]),
                        Group::make([
                            TextInput::make('button2_text')
                                ->label('Second Button Text'),
                            TextInput::make('button2_url')
                                ->label('Second Button URL')
                                ->rule('regex:/^(https?:\/\/.*|\/.*|#.*)/'),
                        ]),
                    ]),

                Toggle::make('reverse_layout')
                    ->label('Reverse Layout')
                    ->default(false),

                TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
