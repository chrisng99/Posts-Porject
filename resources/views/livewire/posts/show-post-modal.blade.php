<x-posts-modal>
    <x-slot name="header">
        {{ __('Post Details') }}
    </x-slot>

    <div class="p-6 text-gray-900">
        <div class="mb-6">
            <x-input-label>Post author:</x-input-label>
            <x-text-input wire:model="author" type="text" class="mt-1 block w-full" disabled />
        </div>
        <div class="mb-6">
            <x-input-label>Post title:</x-input-label>
            <x-text-input wire:model="title" type="text" class="mt-1 block w-full" disabled />
        </div>
        <div class="mb-6">
            <x-input-label>Post category:</x-input-label>
            <x-text-input wire:model="category" type="text" class="mt-1 block w-full" disabled />
        </div>
        <div>
            <x-input-label>Post text:</x-input-label>
            <textarea wire:model="post_text" type="text"
                class="mt-1 h-60 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled></textarea>
        </div>
    </div>
</x-posts-modal>
