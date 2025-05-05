<x-filament::page>
    <div @class(['w-full'])>
        <x-llm-magic::chat.default :messages="$this->getChatMessages()" />
    </div>
</x-filament::page>
