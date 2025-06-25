<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => Notification::make()
                    ->title('Category deleted successfully')
                    ->success()
                    ->send()),
        ];
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Category updated successfully')
            ->success()
            ->send();
    }
}
