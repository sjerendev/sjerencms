<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class TeamMembersBlock extends Block
{
    public static function make(string $name = 'team_members'): static
    {
        return parent::make($name)
            ->label('Team Members')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label('Heading'),
                Forms\Components\TextInput::make('subheading')
                    ->label('Subheading'),
                Forms\Components\MarkdownEditor::make('section_description')
                    ->label('Section Description'),
                Forms\Components\Repeater::make('members')
                    ->label('Team Members')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Forms\Components\TextInput::make('position')
                            ->label('Position')
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Photo')
                            ->image()
                            ->disk('public')
                            ->directory('team-photos')
                            ->required(),
                        Forms\Components\MarkdownEditor::make('bio')
                            ->label('Biography'),
                        Forms\Components\TextInput::make('linkedin')
                            ->label('LinkedIn URL')
                            ->url()
                            ->placeholder('https://linkedin.com/in/username'),
                        Forms\Components\TextInput::make('twitter')
                            ->label('Twitter URL')
                            ->url()
                            ->placeholder('https://twitter.com/username'),
                    ])
                    ->collapsible()
                    ->defaultItems(1)
                    ->required(),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
