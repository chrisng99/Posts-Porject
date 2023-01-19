@props(['post', 'featured'])

<x-card>
    <div class="flex mb-1">
        <h3 class="text-xl font-semibold text-gray-900">{{ $post->title }}</h3>
    </div>
    <footer class="text-sm text-gray-500">
        <p>Written by {{ $post->user->name }} on <time datetime="{{ $post->created_at }}">@datetime($post->created_at)</time></p>
    </footer>

    <p class="mb-7 font-light text-gray-500 whitespace-pre-line">
        @if ($featured)
            {{ $post->post_text }}
        @else
            {{ $post->post_text_truncated }}
        @endif
    </p>

    <div class="flex justify-between">
        <x-button wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode(['post_id' => $post->id]) }})">Read more →</x-button>
        <div class="space-x-0.5 place-self-center">
            @can('edit-post', $post)
                <x-primary-button wire:click="$emit('openModal', 'posts.edit-post-modal', {{ json_encode(['post_id' => $post->id]) }})" type="button">Edit
                </x-primary-button>
            @endcan
            @can('delete-post', $post)
                <form class="inline-block" wire:submit.prevent="destroyPost({{ $post->id }})">
                    @csrf

                    <x-danger-button onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                </form>
            @endcan
        </div>
    </div>
</x-card>
