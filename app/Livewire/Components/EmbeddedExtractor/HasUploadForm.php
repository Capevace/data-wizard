<?php

namespace App\Livewire\Components\EmbeddedExtractor;

use App\Filament\Forms\BucketFileUpload;
use App\Filament\Resources\ExtractionBucketResource;
use App\Models\ExtractionBucket;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HasUploadForm
{
    use InteractsWithForms, InteractsWithActions;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                BucketFileUpload::make('files')
                    ->afterStateUpdated(function (array $state, Set $set) {
                        /** @var ?TemporaryUploadedFile $file */
                        $file = Arr::first($state);
                        $path = $file?->path();
                        $filename = $file?->extractOriginalNameFromFilePath($path);

                        if (! $path) {
                            return;
                        }

                        $this->bucket
                            ->addMedia($path)
                            ->preservingOriginal()
                            ->setName(Str::beforeLast($filename, '.'))
                            ->setFileName(Str::beforeLast($filename, '.'))
                            ->toMediaCollection('files');

                        $this->js('$wire.files = null');
                    })
            ]);
    }
}
