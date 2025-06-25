<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class BlogPostsBlock extends Block
{
    public static function make(string $name = 'blog_posts'): static
    {
        return parent::make($name)
            ->label('Blog Posts')
            ->schema([
                Forms\Components\TextInput::make('section_title')
                    ->label('Section Title')
                    ->required(),
                Forms\Components\Select::make('columns')
                    ->label('Number of Columns')
                    ->options([
                        2 => '2 Columns',
                        3 => '3 Columns',
                        4 => '4 Columns',
                        5 => '5 Columns',
                    ])
                    ->default(3)
                    ->required(),
                Forms\Components\Select::make('categories')
                    ->label('Filter by Categories')
                    ->options(fn () => Category::pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->placeholder('All Categories'),
                Forms\Components\TextInput::make('posts_count')
                    ->label('Number of Posts')
                    ->numeric()
                    ->default(3)
                    ->minValue(1)
                    ->maxValue(10)
                    ->required(),
                Forms\Components\Toggle::make('show_hero_image')
                    ->label('Show Hero Image')
                    ->default(true),
                Forms\Components\Toggle::make('show_date')
                    ->label('Show Post Date')
                    ->default(true),
                Forms\Components\Toggle::make('show_categories')
                    ->label('Show Post Categories')
                    ->default(true),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
