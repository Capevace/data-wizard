@props([
    'language' => 'php',
])

<?php
    $highlighter = new \Tempest\Highlight\Highlighter();
    $code = $highlighter->parse($slot, $language);
?>

<pre {{ $attributes->class('white') }}>{!! $code !!}</pre>
