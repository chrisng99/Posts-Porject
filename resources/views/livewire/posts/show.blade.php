<div class="flex-[0_0_auto] w-3/4 px-3">
    <!-- Featured blog post -->
    @forelse ($posts as $post)
        @if ($loop->first)
            <x-featured-blog-post :$post />
        @endif
    @empty
        <x-card>
            <h2 class="mb-2">No Posts Found</h2>
        </x-card>
    @endforelse

    <!-- Nested row for non-featured blog posts -->
    <div class="flex flex-wrap mt-0 -mx-3">
        <div class="flex-[0_0_auto] w-1/2 px-3">
            <!-- Blog post-->
            @forelse ($posts as $post)
                @if (!$loop->first && $loop->even)
                    <x-blog-post :$post />
                @endif
            @empty
            @endforelse
        </div>
        <div class="flex-[0_0_auto] w-1/2 px-3">
            <!-- Blog post-->
            @forelse ($posts as $post)
                @if (!$loop->first && $loop->odd)
                    <x-blog-post :$post />
                @endif
            @empty
            @endforelse
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
