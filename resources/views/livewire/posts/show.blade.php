<div class="col-lg-9">
    <!-- Featured blog post-->
    @forelse ($posts as $post)
        @if ($loop->first)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="flex items-center mb-1">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $post->title }}</h3>
                    </div>
                    <footer class="mb-5 text-sm text-gray-500">
                        <p>Written by {{ $post->user->name ?? 'Anonymous' }} on <time datetime="{{ $post->created_at }}">@datetime($post->created_at)</time></p>
                    </footer>
                    <p class="mb-2 font-light text-gray-500 whitespace-pre-line">{{ $post->post_text }}</p>
                    <div>
                        <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode(['post_id' => $post->id]) }})"
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
                            <div class="flex items-center mb-1">
                                <h3 class="text-xl font-semibold text-gray-900">{{ $post->title }}</h3>
                            </div>
                            <footer class="mb-3 text-sm text-gray-500">
                                <p>Written by {{ $post->user->name ?? 'Anonymous' }} on <time datetime="{{ $post->created_at }}">@datetime($post->created_at)</time></p>
                            </footer>
                            <p class="mb-2 font-light text-gray-500 whitespace-pre-line">{{ $post->post_text_truncated }}</p>
                            <div>
                                <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode(['post_id' => $post->id]) }})"
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
                            <div class="flex items-center mb-1">
                                <h3 class="text-xl font-semibold text-gray-900">{{ $post->title }}</h3>
                            </div>
                            <footer class="mb-3 text-sm text-gray-500">
                                <p>Written by {{ $post->user->name ?? 'Anonymous' }} on <time datetime="{{ $post->created_at }}">@datetime($post->created_at)</time></p>
                            </footer>
                            <p class="mb-2 font-light text-gray-500 whitespace-pre-line">{{ $post->post_text_truncated }}</p>
                            <div>
                                <a wire:click="$emit('openModal', 'posts.show-post-modal', {{ json_encode(['post_id' => $post->id]) }})"
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
