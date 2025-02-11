<?php

namespace App\Filament\Forms;

use Closure;
use Filament\Forms\Components\Field;

class iFrameField extends Field
{
    protected Closure|float|int|null $zoom = null;

    public function zoom(Closure|float|int $zoom): static
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getZoom(): float|int|null
    {
        return $this->evaluate($this->zoom) ?? 1;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->view('components.forms.iframe-field');
    }
}
