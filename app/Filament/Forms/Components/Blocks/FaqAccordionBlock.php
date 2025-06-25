<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FaqAccordionBlock extends Block
{
    public static function make(string $name = 'faq_accordion'): static
    {
        return parent::make($name)
            ->label('FAQ Accordion')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label('Heading'),
                Forms\Components\TextInput::make('subheading')
                    ->label('Subheading'),
                Forms\Components\Repeater::make('items')
                    ->label('FAQ Items')
                    ->schema([
                        Forms\Components\TextInput::make('question')
                            ->label('Question')
                            ->required(),
                        Forms\Components\MarkdownEditor::make('answer')
                            ->label('Answer')
                            ->required(),
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
