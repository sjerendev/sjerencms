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
use Illuminate\Validation\Rules\Unique;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TracksRelationManager extends RelationManager
{
    protected static string $relationship = 'tracks';

    // A handful of rows: render them with the page instead of after it.
    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Unique within the release, as the (release_id, position) index requires.
                TextInput::make('position')
                    ->integer()
                    ->minValue(1)
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->where('release_id', $this->getOwnerRecord()->getKey())),
                TextInput::make('title')
                    ->required(),
                TextInput::make('duration_seconds')
                    ->integer()
                    ->minValue(1)
                    ->required()
                    ->helperText('Seconds'),
                // The floor the shop checks the buyer's amount against (phase 4).
                TextInput::make('min_price')
                    ->integer()
                    ->minValue(0)
                    ->required()
                    ->default(20)
                    ->suffix('kr'),
                SpatieMediaLibraryFileUpload::make('preview')
                    ->collection('preview')
                    ->acceptedFileTypes(['audio/mpeg'])
                    ->maxSize(1048576)
                    ->saveUploadedFileUsing(self::streamToMedia(...)),
                // The master. Private disk: never reachable from the web root.
                SpatieMediaLibraryFileUpload::make('wav')
                    ->collection('wav')
                    ->disk('music')
                    ->visibility('private')
                    ->acceptedFileTypes(['audio/wav', 'audio/x-wav', 'audio/wave'])
                    ->maxSize(1048576)
                    ->saveUploadedFileUsing(self::streamToMedia(...)),
            ]);
    }

    /**
     * The plugin's own save reads the whole upload into memory
     * (addMediaFromString). Audio is too large for that on a shared host, so
     * hand medialibrary the file and let it copy it as a stream.
     */
    private static function streamToMedia(SpatieMediaLibraryFileUpload $component, TemporaryUploadedFile $file, ?Track $record): ?string
    {
        if (! $record || ! $file->exists()) {
            return null;
        }

        return $record->addMedia($file)
            ->usingFileName($component->getUploadedFileNameForStorage($file))
            ->usingName($component->getMediaName($file) ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->withCustomProperties($component->getCustomProperties($file))
            ->toMediaCollection($component->getCollection() ?? 'default', $component->getDiskName())
            ->getAttributeValue('uuid');
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
                DeleteAction::make()
                    ->hidden(fn (Track $record) => $record->isSold()),
            ]);
    }
}
