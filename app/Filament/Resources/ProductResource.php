<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'E-commerce';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Details')
                    ->schema([
                        Forms\Components\Select::make('product_category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('product_categories', 'slug'),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->directory('products/featured')
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('product_images')
                            ->label('Additional Product Images')
                            ->multiple()
                            ->image()
                            ->directory('products/gallery')
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull()
                            ->reorderable(),
                    ]),
                
                Forms\Components\Section::make('Pricing & Download')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('kr')
                            ->maxValue(42949672.95),
                        Forms\Components\FileUpload::make('download_url')
                            ->label('Product File')
                            ->directory('products/files')
                            ->acceptedFileTypes(['application/json', 'text/plain', 'application/zip'])
                            ->maxSize(50000) // 50MB
                            ->downloadable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Addons')
                    ->schema([
                        Forms\Components\Repeater::make('addons')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required(),
                                Forms\Components\Textarea::make('description'),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('kr'),
                                Forms\Components\Toggle::make('is_active')
                                    ->required(),
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->orderable('sort_order')
                            ->defaultItems(0)
                            ->createItemButtonLabel('Add Service Option'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Display Options')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->square()
                    ->label('Image'),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('sek')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
