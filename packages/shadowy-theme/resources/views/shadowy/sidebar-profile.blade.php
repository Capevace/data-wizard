@props([
    'user' => filament()->auth()->user(),
])

<div
    class="px-6 py-3 bg-gray-100 cursor-pointer dark:bg-gray-950"
    x-data="{
        trigger() {
            this.$el.querySelector('.fi-user-menu button').click();
        }
    }"
>
    <div
        class="border bg-gray-50 flex items-center gap-3 py-2 px-3 rounded-lg shadow -mx-2 mft-sidebar-profile-pill dark:bg-gray-900 dark:border-gray-800"
    >
        <x-filament-panels::user-menu />

        <div class="flex-1" @click="trigger">
            <p class="font-medium text-sm">{{ $user->name }}</p>
            <p class="text-xs text-gray-400">{{ $user->email }}</p>
        </div>

        <x-filament::icon-button
            color="gray"
            icon="heroicon-o-bell"
            icon-alias="panels::topbar.open-database-notifications-button"
            icon-size="lg"
            :label="__('filament-panels::layout.actions.open_database_notifications.label')"
            class="fi-topbar-database-notifications-btn"
            @click.prevent="$dispatch('open-modal', {id: 'database-notifications'})"
        />
    </div>

    <x-filament::icon-button
        color="gray"
        icon="heroicon-o-bell"
        icon-alias="panels::topbar.open-database-notifications-button"
        icon-size="lg"
        :label="__('filament-panels::layout.actions.open_database_notifications.label')"
        class="fi-topbar-database-notifications-btn mft-sidebar-notifications-collapsed"
        @click.prevent="$dispatch('open-modal', {id: 'database-notifications'})"
    />
</div>
