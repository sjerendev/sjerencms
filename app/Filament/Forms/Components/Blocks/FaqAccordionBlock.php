<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class FaqAccordionBlock extends Block
{
    public static function make(?string $name = 'faq_accordion'): static
    {
        return parent::make($name)
            ->label('FAQ Accordion')
            ->schema([
                TextInput::make('heading')
                    ->label('Heading'),
                TextInput::make('subheading')
                    ->label('Subheading'),
                Repeater::make('items')
                    ->label('FAQ Items')
                    ->schema([
                        TextInput::make('question')
                            ->label('Question')
                            ->required(),
                        MarkdownEditor::make('answer')
                            ->label('Answer')
                            ->required(),
                    ])
                    ->collapsible()
                    ->defaultItems(1)
                    ->required(),
                TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
