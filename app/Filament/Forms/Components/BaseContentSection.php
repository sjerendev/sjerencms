<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Closure;

class BaseContentSection extends Section
{
    public static function make(Htmlable|Closure|array|string|null $heading = 'Basic Information'): static
    {
        return parent::make($heading)
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    // Only on create: a new slug on edit would move a live page.
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state ?? ''));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    // Unique per language: every page exists in sv and en under the same slug.
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('language', $get('language'))),
                Select::make('language')
                    ->label('Language')
                    ->options([
                        'sv' => 'Svenska',
                        'en' => 'English',
                    ])
                    ->default('sv')
                    ->required(),
                Toggle::make('is_published')
                    ->default(false),
                TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->placeholder('Leave empty to use page title')
                    ->maxLength(60),
                Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->placeholder('Brief description for search engines')
                    ->maxLength(160)
                    ->rows(2),
            ]);
    }
}
