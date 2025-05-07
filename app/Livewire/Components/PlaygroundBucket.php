<?php

namespace App\Livewire\Components;

use App\Filament\Forms\BucketFileUpload;
use App\Models\ExtractionBucket;
use App\Models\File;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PlaygroundBucket extends Component implements HasForms
{
    use InteractsWithForms;

    #[Locked]
    public string $bucketId;

    public array $files = [];

    #[Computed]
    public function bucket(): ExtractionBucket
    {
        if (!isset($this->bucketId) || ExtractionBucket::where('id', $this->bucketId)->doesntExist()) {
            $this->bucketId = ExtractionBucket::create()->id;
        }

        return ExtractionBucket::findOrFail($this->bucketId);
    }

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

    public function downloadFile(string $fileId)
    {
        /** @var File $file */
        $file = $this->bucket->files()->findOrFail($fileId);

        $path = $file->getPath();

        return response()->file($path, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"',
        ]);
    }

    public function deleteFile(string $fileId): void
    {
        $this->bucket->files()->findOrFail($fileId)->delete();
    }

    public function render()
    {
        return view('components.playground.bucket');
    }
}
