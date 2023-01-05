<x-card>
    <x-slot name="header">Categories</x-slot>

    <ul class="list-none mb-0">
        @forelse ($categories as $category)
            <li class="block">
                <label class="inline-flex items-center">
                    <input wire:model="categoriesFilters" type="checkbox" value="{{ $category['id'] }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-gray-600">{{ $category['name'] }}</span>
                </label>
            </li>
        @empty
            <div>No categories found</div>
        @endforelse
    </ul>
</x-card>
