@props(['tree' => false, 'treeTitle', 'treeIcon', 'treeRoute', 'tableTitle', 'tableIcon', 'tableRoute'])

<x-filament::tabs>
    <x-filament::tabs.item
        tag="a"
        :icon="$treeIcon"
        :href="route($treeRoute)"
        :active="$tree"
    >
        {{ $treeTitle }}
    </x-filament::tabs.item>

    <x-filament::tabs.item
        tag="a"
        :icon="$tableIcon"
        :href="route($tableRoute)"
        :active="! $tree"
    >
        {{ $tableTitle }}
    </x-filament::tabs.item>
</x-filament::tabs>
