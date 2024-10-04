@props([
    /** @var string $path */
    'path',

    'streaming' => false,
])

<span {{$attributes}} @if ($streaming) x-text="getMessageValue('{{ $path }}')" @endif>{{ $slot }}</span>

