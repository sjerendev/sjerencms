<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string | \UnitEnum | null $navigationGroup = 'Shop';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('number')
                    ->state(fn (Order $record) => $record->number()),
                TextEntry::make('email'),
                TextEntry::make('total')
                    ->suffix(' kr'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state) => self::statusColor($state)),
                TextEntry::make('country'),
                TextEntry::make('paid_at')
                    ->dateTime(),
                RepeatableEntry::make('items')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('amount')
                            ->suffix(' kr'),
                        TextEntry::make('download_count')
                            ->label('Downloads'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Number')
                    ->formatStateUsing(fn (Order $record) => $record->number())
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('total')
                    ->suffix(' kr')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state) => self::statusColor($state)),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }

    private static function statusColor(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Paid => 'success',
            OrderStatus::Refunded => 'warning',
            OrderStatus::Unpaid => 'gray',
        };
    }
}
