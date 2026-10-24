<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Models\Project;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

/**
 * An ordered list of projects, stored as `projects: [['project_id' => 3], …]`.
 * Shared by every block that picks projects.
 */
class ProjectPicker
{
    public static function make(string $name = 'projects'): Repeater
    {
        return Repeater::make($name)
            ->schema([
                Select::make('project_id')
                    ->label('Project')
                    ->options(fn () => Project::orderBy('position')->orderBy('id')->pluck('client', 'id'))
                    ->required()
                    ->searchable(),
            ])
            // A pick of a deleted project fails the Select's `in` rule and would block
            // the save, so it is dropped on load. Replaces the Repeater's own hook,
            // hence the hydrateItems() call.
            ->afterStateHydrated(function (Repeater $component): void {
                $live = Project::pluck('id')->all();
                $component->rawState(array_filter(
                    $component->getRawState() ?? [],
                    fn (array $item) => blank($item['project_id'] ?? null) || in_array($item['project_id'], $live),
                ));
                $component->hydrateItems();
            })
            ->reorderable()
            ->defaultItems(0)
            ->addActionLabel('Add project');
    }
}
