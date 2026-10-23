<?php

namespace App\Filament\Resources\Releases\RelationManagers;

use App\Models\Track;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TracksRelationManager extends RelationManager
{
    protected static string $relationship = 'tracks';

    // A handful of rows: render them with the page instead of after it.
    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('position')
                    ->numeric()
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('duration_seconds')
                    ->numeric()
                    ->required()
                    ->helperText('Seconds'),
                TextInput::make('min_price')
                    ->numeric()
                    ->default(20)
                    ->suffix('kr'),
                SpatieMediaLibraryFileUpload::make('preview')
                    ->collection('preview')
                    ->acceptedFileTypes(['audio/mpeg']),
                // The master. Private disk: never reachable from the web root.
                SpatieMediaLibraryFileUpload::make('wav')
                    ->collection('wav')
                    ->disk('music')
                    ->visibility('private')
                    ->acceptedFileTypes(['audio/wav', 'audio/x-wav', 'audio/wave']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('position')
            ->columns([
                TextColumn::make('position'),
                TextColumn::make('title'),
                TextColumn::make('duration_seconds')
                    ->label('Length')
                    ->formatStateUsing(fn (Track $record): string => $record->clock()),
                TextColumn::make('min_price')
                    ->suffix(' kr'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
