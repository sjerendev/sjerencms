<?php

namespace App\Filament\Resources\Pages\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Pages\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\View\View;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Page')
                ->color('success')
                ->action('save'),
            DeleteAction::make()
                ->after(fn () => Notification::make()
                    ->title('Page deleted successfully')
                    ->success()
                    ->send()),
        ];
    }

    public function getHeader(): ?View
    {
        return view('filament.pages.header', [
            'actions' => $this->getCachedHeaderActions(),
            'heading' => $this->getHeading(),
            'subheading' => $this->getSubheading(),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getStickySidebar(): bool
    {
        return true;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Page updated successfully')
            ->success()
            ->send();
    }
}
