{{-- 100% Copy of the original file, just with hint-actions added to the field wrapper --}}

<x-filament-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :hint-actions="$getHintActions()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>

    <div class="code-editor-textarea">

        <div
            style="overflow: auto;"
            @theme-changed.window="function(e) {toggleTheme(e.detail)}"
            @changed-json-state.window="
                if ($event.detail.statePath !== '{{ str($getStatePath())->afterLast('.') }}') {
                    return;
                }

                editor.dispatch({changes: {
                  from: 0,
                  to: editor.state.doc.length,
                  insert: $event.detail.json
                }})
            "
            x-data="codeEditorFormComponent(
                $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
                '{{ $getIsReadOnly() }}',
                '{{ $getDarkModeTheme() }}',
                '{{ $getLightModeTheme() }}'
            )"
        >
            <div wire:ignore class="w-full border border-gray-200 dark:border-gray-600 rounded-lg shadow-inner" x-ref="codeEditor"
                style="height:{{ $getMinHeight() }}px;overflow: hidden; {{ $getCustomStyle() }}">
            </div>
        </div>
    </div>
</x-filament-forms::field-wrapper>
