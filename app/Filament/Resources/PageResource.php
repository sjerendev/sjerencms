<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Filament\Forms\Components\BaseContentSection;
use App\Filament\Forms\Components\ContentBlocksSection;
use App\Filament\Forms\Components\HeroSection;
use App\Filament\Forms\Components\PageSettingsSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                BaseContentSection::make(),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Group::make()
                            ->schema([
                                PageSettingsSection::make(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpan('full'),
                Forms\Components\Group::make()
                    ->schema([
                        HeroSection::make(),
                        ContentBlocksSection::make(),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->selectRaw('pages.*, COALESCE(parent_pages.sort_order, pages.sort_order) as parent_sort_order')
                    ->leftJoin('pages as parent_pages', 'pages.parent_id', '=', 'parent_pages.id')
                    ->orderByRaw('
                        CASE 
                            WHEN pages.parent_id IS NULL THEN pages.sort_order
                            ELSE parent_pages.sort_order
                        END ASC,
                        CASE 
                            WHEN pages.parent_id IS NULL THEN 0
                            ELSE 1
                        END ASC,
                        pages.sort_order ASC
                    ');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->parent_id) {
                            return '<div style="margin-left: 20px;">' . e($record->title) . '</div>';
                        }
                        return $record->title;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Language')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sv' => 'Svenska',
                        'en' => 'English',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sv' => 'primary',
                        'en' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent Page')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_homepage')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label('Language')
                    ->options([
                        'sv' => 'Svenska',
                        'en' => 'English',
                    ])
                    ->placeholder('All Languages')
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->where('pages.language', $data['value']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}