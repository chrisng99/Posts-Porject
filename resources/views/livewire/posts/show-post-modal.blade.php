<x-posts-modal>
    <x-slot name="header">Post Details</x-slot>

    <div class="p-6">
        <article>
            <div class="flex items-center mb-4 space-x-4">
                <div class="space-y-1 text-gray-900">
                    <h1 class="font-bold">{{ $title }}</h1>
                    <span class="inline-block py-0.5 px-1.5 text-sm bg-gray-300">{{ $category }}</span>
                </div>
            </div>
            <footer class="text-sm text-gray-500">
                <p>Written by {{ $author }} on {{ $created_at }}</p>
            </footer>

            <hr class="my-4 mx-auto w-1/2 h-px bg-gray-100 rounded border-0 md:my-10">
            <p class="mb-10 font-normal text-gray-700 whitespace-pre-line">{{ $post_text }}</p>

            <aside>
                <button wire:click="$emit('openModal', 'posts.liked-post-list-modal', {{ json_encode(['post_id' => $post_id]) }})"
                    class="mt-1 text-xs text-gray-500 group hover:underline">
                    <span>{{ $likesCountMessage }}</span>
                    <span class="@if($likesCount == 0) hidden @endif absolute w-auto p-2 m-2 min-w-max bottom-24 rounded-md shadow-md font-normal text-left text-slate-500 bg-gray-200 text-xs transition-all duration-100 scale-0 origin-left group-hover:scale-100">
                        @foreach ($likedUsersArray as $likedUser)
                            @if ($loop->iteration <= 3)
                                <p>{{ $likedUser }}</p>
                            @endif
                        @endforeach
                        @if ($likesCount > 3)
                            <p>...and more</p>
                        @endif
                    </span>
                </button>
                <div class="flex items-center mt-3">
                    <button wire:click="like" class="inline-flex items-center text-sm font-medium {{ $liked ? 'text-blue-600' : 'text-slate-500' }} hover:underline">
                        <svg aria-hidden="true" class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z">
                            </path>
                        </svg>
                        Like
                    </button>
                </div>
            </aside>
        </article>
    </div>
</x-posts-modal>
