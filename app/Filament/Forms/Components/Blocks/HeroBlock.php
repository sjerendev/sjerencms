<?php

namespace App\Filament\Forms\Components\Blocks;

use App\Filament\Forms\Components\MotionSettings;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class HeroBlock extends Block
{
    public static function make(?string $name = 'hero'): static
    {
        return parent::make($name)
            ->label('Hero')
            ->schema([
                TextInput::make('lead')
                    ->required(),
                MotionSettings::make('lead', 'Lead'),
                Textarea::make('phrases')
                    ->required()
                    ->helperText('One per line. They roll after the lead, the first one stands still without motion'),
                Textarea::make('intro_one')
                    ->required(),
                MotionSettings::make('intro_one', 'Intro one'),
                Textarea::make('intro_two')
                    ->required(),
                MotionSettings::make('intro_two', 'Intro two'),
                TextInput::make('work_label')
                    ->required()
                    ->helperText('Links to the projekt page in this language'),
                TextInput::make('cv_label')
                    ->required(),
                // Random name on disk, so the Swedish and English CV never overwrite each other.
                FileUpload::make('cv_file')
                    ->label('CV')
                    ->acceptedFileTypes(['application/pdf'])
                    ->disk('public')
                    ->directory('cv')
                    ->storeFileNamesIn('cv_file_name')
                    ->required()
                    ->helperText('A PDF for this language. It downloads under the name it is uploaded with'),
            ]);
    }
}
