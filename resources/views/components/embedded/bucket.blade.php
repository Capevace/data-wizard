@props([
    /** @var \App\Livewire\Components\EmbeddedExtractor\StepLabels $labels */
    'labels',
])

<form wire:submit.prevent="begin" class="flex flex-col gap-5">
    <div class="mb-4 flex items-start justify-between gap-10">
        <div class="flex-1">
            <x-embedded.header :labels="$labels" />
        </div>

        <x-icon name="heroicon-o-document-duplicate" class="w-14 h-14 text-gray-500 dark:text-gray-400" />
    </div>

    <div>
        {{ $this->form }}
    </div>

    <ul class="flex flex-col gap-5">
        @foreach ($this->bucket->files as $file)
            <li class="flex items-center gap-5 flex-shrink-0 bucket-file">
                <x-forms.image-column-with-loading-indicator
                    :src="$file->thumbnail_src"
                    style="aspect-ratio: 4/3;"
                />
                <div class="flex-1 w-full grid">
                    <p class="font-semibold truncate break-after-all">{{ $file->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $file->humanReadableSize }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ match ($file->type) {
                            'image' => 'Image',
                            'pdf' => 'PDF',
                            default => 'File',
                        } }}
                    </p>
                </div>
                <nav class="flex-shrink-0 flex items-center gap-2">
                    <x-filament::icon-button
                        icon="heroicon-o-cloud-arrow-down"
                        color="gray"
                        size="sm"
                        wire:click="downloadFile('{{ $file->id }}')"
                    />
                    <x-filament::icon-button
                        icon="heroicon-o-trash"
                        color="danger"
                        size="xs"
                        wire:click="deleteFile('{{ $file->id }}')"
                        @click="$el.closest('li').remove()"
                    />

                </nav>
            </li>
        @endforeach
    </ul>

    <x-embedded.buttons :labels="$labels" />
</form>
