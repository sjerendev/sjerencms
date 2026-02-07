<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class StaggerTextBlock extends Block
{
    public static function make(string $name = 'stagger_text'): static
    {
        return parent::make($name)
            ->label('Stagger Text Animation')
            ->schema([
                Forms\Components\Placeholder::make('bold_hint')
                    ->label('')
                    ->content('Use **text** for bold words, e.g. "We help **bold brands** elevate"'),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('text_row_1')
                            ->label('Text Row 1')
                            ->placeholder('We help **bold brands** elevate ideas'),
                        Forms\Components\ColorPicker::make('color_row_1')
                            ->label('Color Row 1')
                            ->default('#9CA3AF'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('text_row_2')
                            ->label('Text Row 2')
                            ->placeholder('through **branding**, **web design** &'),
                        Forms\Components\ColorPicker::make('color_row_2')
                            ->label('Color Row 2')
                            ->default('#111827'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('text_row_3')
                            ->label('Text Row 3')
                            ->placeholder('**development** excellence.'),
                        Forms\Components\ColorPicker::make('color_row_3')
                            ->label('Color Row 3')
                            ->default('#111827'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('text_row_4')
                            ->label('Text Row 4')
                            ->placeholder('Optional fourth row'),
                        Forms\Components\ColorPicker::make('color_row_4')
                            ->label('Color Row 4')
                            ->default('#111827'),
                    ]),
                Forms\Components\ColorPicker::make('background_color')
                    ->label('Background Color')
                    ->default('#F3F4F6'),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('section_height')
                            ->label('Section Height')
                            ->placeholder('100vh')
                            ->helperText('Accepts px, %, vh, svh (e.g. 600px, 100vh, 80svh)'),
                        Forms\Components\Select::make('vertical_align')
                            ->label('Vertical Alignment')
                            ->options([
                                'start' => 'Top',
                                'center' => 'Center',
                                'end' => 'Bottom',
                            ])
                            ->default('start'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('padding_y')
                            ->label('Padding Top/Bottom')
                            ->placeholder('6rem')
                            ->helperText('Vertical padding (e.g. 2rem, 24px, 6rem)'),
                        Forms\Components\TextInput::make('padding_x')
                            ->label('Padding Left/Right')
                            ->placeholder('1.5rem')
                            ->helperText('Horizontal padding (e.g. 1rem, 16px, 1.5rem)'),
                    ]),
                Forms\Components\TextInput::make('font_size')
                    ->label('Font Size (clamp)')
                    ->placeholder('clamp(2.5rem, 5vw, 4.5rem)')
                    ->helperText('Use clamp(min, preferred, max) for responsive sizing'),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('animation_type')
                            ->label('Animation Type')
                            ->options([
                                'scroll_into_view' => 'Scroll Into View (triggers once)',
                                'scroll_scrub' => 'Scroll Scrub (follows scroll direction)',
                            ])
                            ->default('scroll_into_view'),
                        Forms\Components\TextInput::make('stagger_speed')
                            ->label('Stagger Speed (seconds)')
                            ->numeric()
                            ->step(0.01)
                            ->placeholder('0.03')
                            ->helperText('Delay between each letter'),
                    ]),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('section_class')
                            ->label('Section Class')
                            ->placeholder('Custom classes'),
                        Forms\Components\TextInput::make('text_class')
                            ->label('Text Class')
                            ->placeholder('e.g. fw-medium'),
                        Forms\Components\TextInput::make('bold_class')
                            ->label('Bold Class')
                            ->placeholder('e.g. fw-black')
                            ->default('font-bold'),
                    ]),
            ]);
    }
}
