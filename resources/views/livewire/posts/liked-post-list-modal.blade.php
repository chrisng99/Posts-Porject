<x-posts-modal>
    <x-slot name="header">Users who have liked this post</x-slot>

    <div class="p-6">
        <ul class="max-w-md divide-y divide-gray-200">
            @forelse ($likedUsers as $likedUserName => $likedUserEmail)
                @if ($loop->first)
                    <li class="pb-3 sm:pb-4">
                @elseif ($loop->last)
                    <li class="pt-3 pb-0 sm:pt-4">
                @else
                    <li class="py-3 sm:py-4">
                @endif
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $likedUserName }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $likedUserEmail }}
                                </p>
                            </div>
                        </div>
                    </li>
            @empty
                <li>
                    <p class="text-base font-medium text-gray-900 truncate">
                        Nobody has liked this post
                    </p>
                </li>
            @endforelse
        </ul>
    </div>
</x-posts-modal>
