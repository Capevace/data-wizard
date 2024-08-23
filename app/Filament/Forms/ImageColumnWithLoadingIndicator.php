<?php

namespace App\Filament\Forms;

use Filament\Tables\Columns\ImageColumn;

class ImageColumnWithLoadingIndicator extends ImageColumn
{
    protected string $view = 'components.forms.image-column-with-loading-indicator';
}
