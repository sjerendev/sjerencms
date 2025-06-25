<?php

namespace App\Filament\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class PostSettingsSection extends Section
{
    public static function make(Htmlable|\Closure|array|string|null $heading = 'Post Settings'): static
    {
        return parent::make($heading)
            ->schema([
                Forms\Components\FileUpload::make('list_image')
                    ->label('List Image')
                    ->helperText('This image will be used in blog lists and cards. If not set, the hero image will be used.')
                    ->image()
                    ->imageEditor()
                    ->directory('posts')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('publish_date')
                    ->label('Publish Date')
                    ->helperText('Set when this post should be published. Leave empty to use creation date.')
                    ->native(false)
                    ->seconds(false),
                Forms\Components\Select::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload()
                    ->searchable(),
            ]);
    }
}
