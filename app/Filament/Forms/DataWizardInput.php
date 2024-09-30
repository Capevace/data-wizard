<?php

namespace App\Filament\Forms;

use Closure;
use Filament\Forms\Components\Field;

class DataWizardInput extends Field
{
    protected string $view = 'components.forms.data-wizard-input';

    public Closure|string $extractorId;
    public Closure|string|null $bucketId = null;
    public Closure|string|null $runId = null;
    public Closure|string|null $signature = null;

    protected function setUp(): void
    {
    }
}
