<x-app-layout>
    <x-slot name="header">
        <div class="sm:flex sm:justify-between">
            <div class="inline-flex gap-12">
                <h2 class="font-semibold text-xl text-gray-800 place-self-center">Posts</h2>
                <x-button onclick="Livewire.emit('showAllPostsEvent')">All Posts</x-button>
                <x-button onclick="Livewire.emit('showMyPostsEvent')">My Posts</x-button>
            </div>
            <x-button onclick="Livewire.emit('openModal', 'posts.create-post-modal')">Create New Post</x-button>
        </div>
    </x-slot>

    <div class="flex flex-wrap mt-0 -mx-3">
        <!-- Blog entries-->
        @livewire('posts.show')

        <div class="flex-[0_0_auto] w-1/4 px-3">
            <!-- Search widget-->
            @livewire('posts.search')

            <!-- Categories widget-->
            @livewire('posts.categories-widget')
            
            <!-- Side widget-->
            <x-card>
                <x-slot name="header">Side Widget</x-slot>
                You can put anything you want inside of these side widgets. They are easy to use, and feature the Bootstrap 5 card component!
            </x-card>
        </div>
    </div>
</x-app-layout>
