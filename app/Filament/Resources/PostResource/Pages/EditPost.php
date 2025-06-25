<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\View\View;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Save Post')
                ->color('success')
                ->action('save'),
            Actions\DeleteAction::make()
                ->after(fn () => Notification::make()
                    ->title('Post deleted successfully')
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
            ->title('Post updated successfully')
            ->success()
            ->send();
    }
}
