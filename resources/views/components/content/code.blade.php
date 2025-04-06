@php use Tempest\Highlight\Themes\CssTheme;use Tempest\Highlight\Themes\InlineTheme; @endphp
@props([
    'language' => 'php',
    'theme' => null
])

<?php
$theme = $theme === null
    ? new CssTheme
    : new InlineTheme(base_path("vendor/tempest/highlight/src/Themes/Css/{$theme}.css"));

$highlighter = new \Tempest\Highlight\Highlighter($theme);
$code = $highlighter->parse($slot, $language);
?>

<pre {{ $attributes->class('white') }}>{!! $code !!}</pre>
