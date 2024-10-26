@props([
    'label',
    'description' => null,

    /** @var ?string $icon */
    'icon' => null,
    'chevronIcon' => 'heroicon-o-chevron-down',

    'color' => 'gray',

    'chartJsConfig' => [],
])


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<x-llm-magic::tools.dropdown
    :$icon
    :$color
    :$label
    :$description
    :$chevronIcon
    :collapsed="false"
>
    <div
        x-data="{
            init() {
                this.chart = new Chart(this.$refs.chart.getContext('2d'), @js($chartJsConfig));
            },

            destroy() {
                this.chart.destroy();
            }
        }"
    >
        <canvas x-ref="chart" class="w-full h-full"></canvas>
    </div>
</x-tools.dropdown>
