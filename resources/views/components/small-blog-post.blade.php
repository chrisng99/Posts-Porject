@props(['post'])

<div class="card mb-4">
    <img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." />
    <div class="card-body">
        <div class="small text-muted">{{ $post->created_at }}</div>
        <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}
        </div>
        <h2 class="card-title h4">{{ $post->title }}</h2>
        <p class="card-text">{{ $post->post_text_truncated }}</p>
        <div>
            <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode(['post' => $post->id]) }})" class="btn btn-primary mt-4">Read more
                →</a>
            <div class="inline-flex float-right mt-4">
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}">
                        <x-primary-button>Edit</x-primary-button>
                    </a>
                @endcan
                @can('delete', $post)
                    <form class="ml-2" method="POST" action="{{ route('posts.destroy', $post) }}">
                        @method('DELETE')

                        <x-danger-button onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
