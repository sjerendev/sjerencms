<?php

namespace App\Filament\Resources\Releases\Pages;

use App\Filament\Resources\Releases\ReleaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRelease extends EditRecord
{
    protected static string $resource = ReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Sold releases cannot be deleted (the model refuses too); unpublish them.
            DeleteAction::make()
                ->hidden(fn ($record) => $record->isSold()),
        ];
    }

    // The admin routes on slug, so a new slug needs the new address or a reload 404s.
    protected function getRedirectUrl(): ?string
    {
        return $this->getRecord()->wasChanged('slug')
            ? static::getResource()::getUrl('edit', ['record' => $this->getRecord()])
            : parent::getRedirectUrl();
    }
}
