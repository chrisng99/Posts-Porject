<x-posts-modal>
    <x-slot name="header">
        {{ __('Create New Post') }}
    </x-slot>

    <div class="p-6 text-gray-900">
        <form wire:submit.prevent="submit">
            @csrf

            <div class="mb-6">
                <x-input-label>Post title:</x-input-label>
                <x-text-input wire:model="title" type="text" class="mt-1 block w-full" autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div class="mb-6">
                <x-input-label>Post category:</x-input-label>
                <select wire:model="category_id" class="p-1 mt-1 px-3 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-56">
                    <option value=''>Choose a category</option>
                    @forelse ($categories as $category)
                        <option value={{ $category['id'] }}>{{ $category['name'] }}</option>
                    @empty
                    @endforelse
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>
            <div class="mb-6">
                <x-input-label>Post text:</x-input-label>
                <textarea wire:model="post_text" type="text"
                    class="mt-1 h-60 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" autofocus></textarea>
                <x-input-error :messages="$errors->get('post_text')" class="mt-2" />
            </div>
            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-posts-modal>
