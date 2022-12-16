<x-posts-modal>
    <x-slot name="header">
        {{ __('Show Posts') }}
    </x-slot>

    <div class="p-6 text-gray-900">
        <div class="mb-6">
            <x-input-label>Post title:</x-input-label>
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ $post->title }}" disabled />
        </div>
        <div class="mb-6">
            <x-input-label>Post category:</x-input-label>
            <x-text-input id="category_id" name="category_id" type="text" class="mt-1 block w-full" value="{{ $post->category->name }}" disabled />
        </div>
        <div class="mb-6">
            <x-input-label>Post text:</x-input-label>
            <textarea id="post_text" name="post_text" type="text"
                class="mt-1 h-60 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled>{{ $post->post_text }}</textarea>
        </div>
        <div>
            <x-input-label>Post author:</x-input-label>
            <x-text-input id="user_id" name="user_id" type="text" class="mt-1 block w-full" value="{{ $post->user->name ?? 'Anonymous' }}" disabled />
        </div>
    </div>
</x-posts-modal>
