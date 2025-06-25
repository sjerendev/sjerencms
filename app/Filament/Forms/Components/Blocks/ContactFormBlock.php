<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ContactFormBlock extends Block
{
    public static function make(string $name = 'contact_form'): static
    {
        return parent::make($name)
            ->label('Contact Form')
            ->schema([
                Forms\Components\TextInput::make('heading')
                    ->label('Heading'),
                Forms\Components\TextInput::make('subheading')
                    ->label('Subheading'),
                Forms\Components\MarkdownEditor::make('section_description')
                    ->label('Section Description'),
                Forms\Components\TextInput::make('notification_email')
                    ->label('Notification Email')
                    ->email()
                    ->default('info@kalibercreative.se')
                    ->required()
                    ->helperText('Email address where form submissions will be sent'),
                Forms\Components\TextInput::make('submit_button_text')
                    ->label('Submit Button Text')
                    ->default('Skicka meddelande')
                    ->required(),
                Forms\Components\TextInput::make('success_message')
                    ->label('Success Message')
                    ->default('Tack för ditt meddelande. Vi kommer att kontakta dig snart!')
                    ->required(),
                Forms\Components\TextInput::make('error_message')
                    ->label('Error Message')
                    ->default('Tyvärr uppstod ett fel när meddelandet skulle skickas. Vänligen försök igen senare.')
                    ->required(),
                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class Name')
                    ->placeholder('Add custom CSS classes'),
            ]);
    }
}
