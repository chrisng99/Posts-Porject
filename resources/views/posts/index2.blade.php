<x-app-layout>
    <x-slot name="header">
        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Posts') }}
            </h2>
        </div>
    </x-slot>

    <div class="container">
        <div class="row">
            <!-- Blog entries-->
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
            <!-- Side widgets-->
            <div class="col-lg-3">
                <!-- Search widget-->
                <div class="card mb-4">
                    <div class="card-header">Search</div>
                    <div class="card-body">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Enter search term..." aria-label="Enter search term..." aria-describedby="button-search" />
                            <button class="btn btn-primary" id="button-search" type="button">Go!</button>
                        </div>
                    </div>
                </div>
                <!-- Categories widget-->
                <div class="card mb-4">
                    <div class="card-header">Categories</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="list-group-numbered mb-0">
                                    @foreach ($categories as $category)
                                    <li>{{ $category->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add post widget-->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <a class="inline-flex items-center px-4 py-2 my-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            href="{{ route('posts.create') }}">
                            Add New Post
                        </a>
                        <a class="inline-flex items-center px-4 py-2 my-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            href="#">
                            Manage Posts
                        </a>
                    </div>
                </div>
                <!-- Side widget-->
                <div class="card mb-4">
                    <div class="card-header">Side Widget</div>
                    <div class="card-body">You can put anything you want inside of these side widgets. They are easy to use, and feature the Bootstrap 5 card component!</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>