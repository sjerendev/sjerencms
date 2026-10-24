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
            ->reorderable()
            ->defaultItems(0)
            ->addActionLabel('Add project');
    }
}
