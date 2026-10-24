<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    // The model routes on slug for the public site; the admin keeps ids.
    protected static ?string $recordRouteKeyName = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state ?? ''));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('headline_sv')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('headline_en')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('role_sv'),
                TextInput::make('role_en'),
                TagsInput::make('services')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('tech'),
                TextInput::make('url')
                    ->url()
                    ->required(),
                Toggle::make('rebuilt')
                    ->helperText('Rebuilt by someone else since: no link'),
                TextInput::make('position')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('image')
                    ->image(),
                SpatieMediaLibraryFileUpload::make('preview')
                    ->collection('preview')
                    ->image()
                    ->helperText('Portrait, 450×526, shown on hover in the register'),
                SpatieMediaLibraryFileUpload::make('tall')
                    ->collection('tall')
                    ->image()
                    ->helperText('Full-page screenshot. Pins the case and scrolls inside its frame'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('preview')
                    ->collection('preview'),
                TextColumn::make('client')
                    ->searchable(),
                TextColumn::make('headline_sv'),
                TextColumn::make('position'),
            ])
            ->reorderable('position')
            ->defaultSort('position')
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
