<?php

namespace App\Filament\Resources\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Filament\Forms\Components\BaseContentSection;
use App\Filament\Forms\Components\ContentBlocksSection;
use App\Filament\Forms\Components\HeroSection;
use App\Filament\Forms\Components\PageSettingsSection;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                BaseContentSection::make(),
                            ])
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                PageSettingsSection::make(),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpan('full'),
                Group::make()
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
                TextColumn::make('title')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->parent_id) {
                            return '<div style="margin-left: 20px;">' . e($record->title) . '</div>';
                        }
                        return $record->title;
                    })
                    ->html(),
                TextColumn::make('language')
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
                TextColumn::make('sort_order')
                    ->sortable(),
                TextColumn::make('parent.title')
                    ->label('Parent Page')
                    ->searchable(),
                IconColumn::make('is_published')
                    ->boolean(),
                IconColumn::make('is_homepage')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('language')
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}