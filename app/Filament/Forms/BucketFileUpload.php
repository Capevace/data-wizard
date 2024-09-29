<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\FileUpload;

class BucketFileUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Files')
            ->disk('local')
            ->preserveFilenames()
            ->directory('uploads')
            ->translateLabel()
            ->multiple()
            ->imageEditor()
            ->maxSize(1024 * 1024 * 20);
    }
}
