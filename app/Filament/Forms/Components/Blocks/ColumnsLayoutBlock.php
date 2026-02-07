<?php

namespace App\Filament\Forms\Components\Blocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;

class ColumnsLayoutBlock extends Block
{
    public static function make(string $name = 'columns_layout'): static
    {
        return parent::make($name)
            ->label('Columns Layout')
            ->schema([
                Forms\Components\Select::make('columns')
                    ->label('Number of Columns')
                    ->options([
                        '2' => '2 Columns',
                        '3' => '3 Columns',
                        '4' => '4 Columns',
                    ])
                    ->default('2')
                    ->live(),
                Forms\Components\Select::make('column_ratio')
                    ->label('Column Ratio')
                    ->options(fn (Forms\Get $get) => match ($get('columns')) {
                        '2' => [
                            '1-1' => 'Equal (1:1)',
                            '1-2' => 'Narrow + Wide (1:2)',
                            '2-1' => 'Wide + Narrow (2:1)',
                        ],
                        '3' => [
                            '1-1-1' => 'Equal (1:1:1)',
                            '2-1-1' => 'Wide + Narrow + Narrow',
                            '1-2-1' => 'Narrow + Wide + Narrow',
                        ],
                        '4' => [
                            '1-1-1-1' => 'Equal (1:1:1:1)',
                        ],
                        default => ['1-1' => 'Equal'],
                    })
                    ->default('1-1'),
                Forms\Components\Section::make('Responsive Columns')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('columns_mobile')
                                    ->label('Mobile')
                                    ->options([
                                        '1' => '1 Column (stacked)',
                                        '2' => '2 Columns',
                                    ])
                                    ->default('1'),
                                Forms\Components\Select::make('columns_tablet')
                                    ->label('Tablet')
                                    ->options([
                                        '1' => '1 Column',
                                        '2' => '2 Columns',
                                        '3' => '3 Columns',
                                    ])
                                    ->default('2'),
                                Forms\Components\Select::make('columns_desktop')
                                    ->label('Desktop')
                                    ->helperText('Uses main column settings')
                                    ->disabled()
                                    ->placeholder('From settings above'),
                            ]),
                    ])
                    ->collapsed(),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('gap')
                            ->label('Column Gap')
                            ->placeholder('2rem')
                            ->helperText('Space between columns'),
                        Forms\Components\Select::make('vertical_align')
                            ->label('Vertical Align')
                            ->options([
                                'start' => 'Top',
                                'center' => 'Center',
                                'end' => 'Bottom',
                                'stretch' => 'Stretch',
                            ])
                            ->default('start'),
                    ]),
                Forms\Components\ColorPicker::make('background_color')
                    ->label('Background Color'),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('padding_y')
                            ->label('Padding Top/Bottom')
                            ->placeholder('4rem'),
                        Forms\Components\TextInput::make('padding_x')
                            ->label('Padding Left/Right')
                            ->placeholder('1.5rem'),
                    ]),

                // Column 1
                Forms\Components\Section::make('Column 1')
                    ->schema([
                        Forms\Components\Builder::make('column_1')
                            ->blocks(self::getColumnBlocks())
                            ->collapsible()
                            ->blockNumbers(false),
                    ])
                    ->collapsible(),

                // Column 2
                Forms\Components\Section::make('Column 2')
                    ->schema([
                        Forms\Components\Builder::make('column_2')
                            ->blocks(self::getColumnBlocks())
                            ->collapsible()
                            ->blockNumbers(false),
                    ])
                    ->collapsible(),

                // Column 3
                Forms\Components\Section::make('Column 3')
                    ->schema([
                        Forms\Components\Builder::make('column_3')
                            ->blocks(self::getColumnBlocks())
                            ->collapsible()
                            ->blockNumbers(false),
                    ])
                    ->collapsible()
                    ->visible(fn (Forms\Get $get) => in_array($get('columns'), ['3', '4'])),

                // Column 4
                Forms\Components\Section::make('Column 4')
                    ->schema([
                        Forms\Components\Builder::make('column_4')
                            ->blocks(self::getColumnBlocks())
                            ->collapsible()
                            ->blockNumbers(false),
                    ])
                    ->collapsible()
                    ->visible(fn (Forms\Get $get) => $get('columns') === '4'),

                Forms\Components\TextInput::make('section_class')
                    ->label('Section Class')
                    ->placeholder('Custom CSS classes'),
            ]);
    }

    /**
     * Get blocks available inside columns (excludes layout blocks to prevent infinite nesting)
     */
    protected static function getColumnBlocks(): array
    {
        return [
            FullTextBlock::make(),
            HeadlineTextBlock::make(),
            FullImageBlock::make(),
            FaqAccordionBlock::make(),
            IconCardsBlock::make(),
            CallToActionBlock::make(),
            VideoBlock::make(),
            StaggerTextBlock::make(),
        ];
    }
}
