<div
    x-data="{
        dragging: false,
        handleDrop(event) {
            const files = event.dataTransfer.files;

            if (files.length > 0) {
                // Access the Livewire component instance and call the upload method
                // 'files' corresponds to the public property $files in the component
                this.$wire.uploadMultiple('files', files,
                    (uploadedFilename) => {
                        // Success callback for each file if needed
                        console.log('Uploaded:', uploadedFilename);
                    },
                    (error) => {
                        // Error callback
                        console.error('Upload error:', error);
                    },
                    (event) => {
                        // Progress callback (event.detail.progress)
                        console.log('Progress:', event.detail.progress);
                        // Livewire's built-in progress tracking for wire:model should handle this automatically
                        // but you can use this for custom logic if needed.
                    }
                );
            }
        }
    }"
    x-on:dragenter="dragging = true"
    @dragover.prevent="dragging = true"
    @dragleave.prevent="dragging = false"
    @drop.prevent="dragging = false; handleDrop($event)"
    class="border border-dashed rounded-md text-center cursor-pointer transition-all"
    :class="{
        'border-primary-500 dark:border-primary-500': dragging,
        'border-gray-300 dark:border-gray-600': !dragging,
    }"
>
    <input
        x-ref="fileInput"
        type="file"
        multiple
        wire:model="files"
        class="hidden"
    />

    @if (count($this->bucket->files) === 0)
        <div class="flex items-center justify-center min-h-32 bg-gray-700 rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Drag & drop your files here</p>
        </div>
    @endif

    <ul class="flex flex-col gap-5">
        @foreach ($this->bucket->files as $file)
            <li class="flex items-center gap-5 flex-shrink-0 bucket-file pr-5">
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
</div>
