<?php

return [
    'filament' => [
        'repeater' => \Filament\Forms\Components\Repeater::class,
        'grid' => \Filament\Forms\Components\Grid::class,
        'text-input' => \Filament\Forms\Components\TextInput::class,
        'toggle' => \Filament\Forms\Components\Toggle::class,
        'hidden' => \Filament\Forms\Components\Hidden::class,
        'table' => [
            'text-column' => \Filament\Tables\Columns\TextColumn::class,
            'text-input-column' => \Filament\Tables\Columns\TextInputColumn::class,
            'icon-column' => \Filament\Tables\Columns\IconColumn::class,
        ],
        'infolist' => [
            'text-entry' => \Filament\Infolists\Components\TextEntry::class,
            'icon-entry' => \Filament\Infolists\Components\IconEntry::class,
            'grid' => \Filament\Infolists\Components\Grid::class,
        ],
    ],
];
