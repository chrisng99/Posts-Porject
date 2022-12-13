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
            <a class="btn btn-primary mt-4" href="{{ route('posts.show', $post->id) }}">Read more →</a>
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
            @if(!$loop->first && $loop->even)
            <div class="card mb-4">
                <img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." />
                <div class="card-body">
                    <div class="small text-muted">{{ $post->created_at }}</div>
                    <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}</div>
                    <h2 class="card-title h4">{{ $post->title }}</h2>
                    <p class="card-text">{{ $post->post_text_truncated }}</p>
                    <a class="btn btn-primary mt-4" href="{{ route('posts.show', $post->id) }}">Read more →</a>
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
                <img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." />
                <div class="card-body">
                    <div class="small text-muted">{{ $post->created_at }}</div>
                    <div class="small text-muted fst-italic">Written By {{ $post->user->name ?? 'Anonymous' }}</div>
                    <h2 class="card-title h4">{{ $post->title }}</h2>
                    <p class="card-text">{{ $post->post_text_truncated }}</p>
                    <a class="btn btn-primary mt-4" href="{{ route('posts.show', $post->id) }}">Read more →</a>
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