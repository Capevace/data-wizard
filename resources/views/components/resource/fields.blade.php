@props([
    /** @var array<string, string> $data */
    'data' => []
])

<?php
$data = collect($data)
    ->sortBy(fn ($value) => str_word_count($value))
    ->toArray();
?>

<div class="grid grid-cols-3 gap-5">
    @foreach ($data as $label => $value)
        <x-llm-magic::resource.field :$label :$value />
    @endforeach

    {{ $slot }}
</div>
