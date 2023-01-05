<x-card>
    <x-slot name="header">Search</x-slot>

    <div class="relative flex flex-wrap items-stretch w-full">
        <input class="block w-full py-1.5 px-3 text-base font-normal leading-6 text-slate-800 bg-white bg-clip-padding border border-solid border-slate-300 rounded appearance-none"
            wire:model.debounce.500ms="search" type="text" placeholder="Enter search term..." aria-label="Enter search term..." />
    </div>
</x-card>
