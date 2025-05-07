@props([
    'error' => null,
])

@if ($title = $error['title'] ?? null)
    <p class="text-sm font-semibold text-danger-500 dark:text-danger-400">{{ $title }}</p>
@endif
@if (($errorMessage = $error['message'] ?? null) && $errorMessage !== $title)
    <p class="text-sm text-danger-500 dark:text-danger-400 mb-2">{{ $errorMessage }}</p>
@endif
@if ($trace = $error['trace'] ?? null)
    <pre class="mt-1 text-xs text-gray-500 dark:text-gray-400 overflow-x-auto bg-transparent">{{ $trace }}</pre>
@endif
