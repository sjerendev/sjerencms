<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Page;
use Filament\Forms;
use Illuminate\Contracts\Support\Htmlable;
use Closure;

class PageSettingsSection extends Section
{
    public static function make(Htmlable|Closure|array|string|null $heading = 'Page Settings'): static
    {
        return parent::make($heading)
            ->schema([
                Select::make('parent_id')
                    ->label('Parent Page')
                    ->relationship('parent', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(10)
                    ->helperText('Pages are ordered by this number. Use any number to order pages (e.g., 1, 2, 5, 10, etc.).'),
                Toggle::make('is_homepage')
                    ->label('Set as Homepage')
                    ->default(false)
                    ->afterStateUpdated(function ($state, Set $set, $record) {
                        if ($state) {
                            Page::where('is_homepage', true)
                                ->where('id', '!=', $record?->id)
                                ->update(['is_homepage' => false]);
                        }
                    }),
                Toggle::make('show_in_main_nav')
                    ->label('Show in Main Navigation')
                    ->default(false)
                    ->helperText('Display this page in the main navigation menu'),
                Toggle::make('show_in_footer_nav')
                    ->label('Show in Footer Navigation')
                    ->default(false)
                    ->helperText('Display this page in the footer navigation menu'),
            ]);
    }
}
