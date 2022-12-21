<div class="col-lg-9">
    <!-- Featured blog post-->
    @forelse ($posts as $post)
        @if ($loop->first)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="small text-muted">{{ $post->created_at }}</div>
                    <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}</div>
                    <h2 class="card-title">{{ $post->title }}</h2>
                    <p class="card-text">{{ $post->post_text }}</p>
                    <div>
                        <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode([$post->user->name ?? 'Anonymous', $post->title, $post->category->name, $post->post_text]) }})"
                            class="btn btn-primary mt-4">Read
                            more
                            →</a>
                        @can('manage-post', $post)
                            <div class="inline-flex float-right mt-4">
                                <a wire:click="$emit('openModal', 'posts.edit-post-modal', {{ json_encode(['post_id' => $post->id]) }})">
                                    <x-primary-button>Edit</x-primary-button>
                                </a>
                                <form class="ml-2" wire:submit.prevent="destroyPost({{ $post->id }})">
                                    @csrf

                                    <x-danger-button onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                                </form>
                            </div>
                        @endcan
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
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="small text-muted">{{ $post->created_at }}</div>
                            <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}
                            </div>
                            <h2 class="card-title h4">{{ $post->title }}</h2>
                            <p class="card-text">{{ $post->post_text_truncated }}</p>
                            <div>
                                <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode([$post->user->name ?? 'Anonymous', $post->title, $post->category->name, $post->post_text]) }})"
                                    class="btn btn-primary mt-4">Read more
                                    →</a>
                                @can('manage-post', $post)
                                    <div class="inline-flex float-right mt-4">
                                        <a wire:click="$emit('openModal', 'posts.edit-post-modal', {{ json_encode(['post_id' => $post->id]) }})">
                                            <x-primary-button>Edit</x-primary-button>
                                        </a>
                                        <form class="ml-2" wire:submit.prevent="destroyPost({{ $post->id }})">
                                            @csrf

                                            <x-danger-button onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endif
            @empty
            @endforelse
        </div>
        <div class="col-lg-6">
            <!-- Blog post-->
            @forelse ($posts as $post)
                @if (!$loop->first && $loop->odd)
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="small text-muted">{{ $post->created_at }}</div>
                            <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}
                            </div>
                            <h2 class="card-title h4">{{ $post->title }}</h2>
                            <p class="card-text">{{ $post->post_text_truncated }}</p>
                            <div>
                                <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode([$post->user->name ?? 'Anonymous', $post->title, $post->category->name, $post->post_text]) }})"
                                    class="btn btn-primary mt-4">Read more
                                    →</a>
                                @can('manage-post', $post)
                                    <div class="inline-flex float-right mt-4">
                                        <a wire:click="$emit('openModal', 'posts.edit-post-modal', {{ json_encode(['post_id' => $post->id]) }})">
                                            <x-primary-button>Edit</x-primary-button>
                                        </a>
                                        <form class="ml-2" wire:submit.prevent="destroyPost({{ $post->id }})">
                                            @csrf

                                            <x-danger-button onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endif
            @empty
            @endforelse
        </div>
    </div>
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
