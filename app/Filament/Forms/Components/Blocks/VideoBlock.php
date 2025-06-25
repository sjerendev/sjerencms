<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class VideoBlock extends Block
{
    public static function make(string $name = 'video'): static
    {
        return parent::make($name)
            ->label('Video')
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Video Type')
                    ->options([
                        'embed' => 'Embed URL (YouTube, Vimeo)',
                        'file' => 'Upload Video File',
                    ])
                    ->default('embed')
                    ->reactive()
                    ->required(),
                Forms\Components\TextInput::make('embed_url')
                    ->label('Embed URL')
                    ->url()
                    ->placeholder('https://youtube.com/watch?v=xxx or https://vimeo.com/xxx')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'embed')
                    ->required(fn (Forms\Get $get) => $get('type') === 'embed'),
                Forms\Components\FileUpload::make('video_file')
                    ->label('Video File')
                    ->disk('public')
                    ->directory('videos')
                    ->acceptedFileTypes(['video/*'])
                    ->visible(fn (Forms\Get $get) => $get('type') === 'file')
                    ->required(fn (Forms\Get $get) => $get('type') === 'file'),
                Forms\Components\TextInput::make('title')
                    ->label('Title'),
                Forms\Components\MarkdownEditor::make('description')
                    ->label('Description'),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
