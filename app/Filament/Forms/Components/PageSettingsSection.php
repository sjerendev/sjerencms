<?php

namespace App\Filament\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class PageSettingsSection extends Section
{
    public static function make(Htmlable|\Closure|array|string|null $heading = 'Page Settings'): static
    {
        return parent::make($heading)
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Page')
                    ->relationship('parent', 'title')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(10)
                    ->helperText('Pages are ordered by this number. Use any number to order pages (e.g., 1, 2, 5, 10, etc.).'),
                Forms\Components\Toggle::make('is_homepage')
                    ->label('Set as Homepage')
                    ->default(false)
                    ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                        if ($state) {
                            \App\Models\Page::where('is_homepage', true)
                                ->where('id', '!=', $record?->id)
                                ->update(['is_homepage' => false]);
                        }
                    }),
                Forms\Components\Toggle::make('show_in_main_nav')
                    ->label('Show in Main Navigation')
                    ->default(false)
                    ->helperText('Display this page in the main navigation menu'),
                Forms\Components\Toggle::make('show_in_footer_nav')
                    ->label('Show in Footer Navigation')
                    ->default(false)
                    ->helperText('Display this page in the footer navigation menu'),
            ]);
    }
}
