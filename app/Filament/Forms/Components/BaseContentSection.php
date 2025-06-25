<?php

namespace App\Filament\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Closure;

class BaseContentSection extends Section
{
    public static function make(Htmlable|Closure|array|string|null $heading = 'Basic Information'): static
    {
        return parent::make($heading)
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(string $state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Toggle::make('is_published')
                    ->default(false),
                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->placeholder('Leave empty to use page title')
                    ->maxLength(60),
                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->placeholder('Brief description for search engines')
                    ->maxLength(160)
                    ->rows(2),
            ]);
    }
}
