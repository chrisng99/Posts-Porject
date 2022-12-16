<div class="col-lg-9">
    <!-- Featured blog post-->
    @forelse ($posts as $post)
        @if ($loop->first)
            <div class="card mb-4">
                <img class="card-img-top" src="https://dummyimage.com/850x350/dee2e6/6c757d.jpg" alt="..." />
                <div class="card-body">
                    <div class="small text-muted">{{ $post->created_at }}</div>
                    <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}</div>
                    <h2 class="card-title">{{ $post->title }}</h2>
                    <p class="card-text">{{ $post->post_text }}</p>
                    <div>
                        <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode(['post' => $post->id]) }})" class="btn btn-primary mt-4">Read
                            more
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
        @endif
    @empty
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">No Posts Found</h2>
            </div>
        </div>
    @endforelse
    <!-- Nested row for non-featured blog posts-->
    <div class="row">
        <div class="col-lg-6">
            <!-- Blog post-->
            @forelse ($posts as $post)
                @if (!$loop->first && $loop->even)
                    <x-small-blog-post :post="$post" />
                @endif
            @empty
            @endforelse
        </div>
        <div class="col-lg-6">
            <!-- Blog post-->
            @forelse ($posts as $post)
                @if (!$loop->first && $loop->odd)
                    <x-small-blog-post :post="$post" />
                @endif
            @empty
            @endforelse
        </div>
    </div>
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
