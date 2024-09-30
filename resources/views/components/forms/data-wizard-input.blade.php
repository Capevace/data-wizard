@props([
    'extractorId',
    'bucketId' => null,
    'runId' => null,
    'signature' => null,
])

<div>
    <livewire:embedded-extractor
        :extractor-id="$extractorId"
        :bucket-id="$bucketId"
        :run-id="$runId"
        :signature="$signature"
    />
</div>
