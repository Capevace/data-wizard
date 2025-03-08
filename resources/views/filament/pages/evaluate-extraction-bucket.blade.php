<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$this->model === null"
            alpine-active="$wire.model === null"
            wire:click="$set('model', null)"
            wire:key="all"
        >
            {{ __('All') }}
        </x-filament::tabs.item>

        @foreach ($this->model_tabs as $model => $label)
            <x-filament::tabs.item
                :active="$this->model === $model"
                alpine-active="$wire.model === '{{ $model }}'"
                wire:click="$set('model', '{{ $model }}')"
                wire:key="{{ $model }}"
            >
                {{ $label }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <x-filament-widgets::widgets
        :data="$this->getWidgetData()"
        :widgets="$this->getStatisticsWidgets()"
        class="fi-page-header-widgets"
    />

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
            :content-tab-label="$this->getContentTabLabel()"
            :content-tab-icon="$this->getContentTabIcon()"
            :content-tab-position="$this->getContentTabPosition()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($hasCombinedRelationManagerTabsWithContent)
                <x-slot name="content">
                    @if ($this->hasInfolist())
                        {{ $this->infolist }}
                    @else
                        {{ $this->form }}
                    @endif
                </x-slot>
            @endif
        </x-filament-panels::resources.relation-managers>
    @endif
</x-filament-panels::page>
