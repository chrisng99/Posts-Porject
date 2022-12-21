<x-app-layout>
    <x-slot name="header">
        <div class="hidden sm:flex -mx-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight p-header mx-6">
                {{ __('Posts') }}
            </h2>
            <div class="font-semibold text-xl text-gray-800 leading-tight mx-6">
                <a onclick="Livewire.emit('showAllPostsEvent')" class="btn btn-primary">All Posts</a>
            </div>
            <div class="font-semibold text-xl text-gray-800 leading-tight mx-6">
                <a onclick="Livewire.emit('showMyPostsEvent')" class="btn btn-primary">My Posts</a>
            </div>
            <div class="font-semibold text-xl text-gray-800 ml-auto">
                <a onclick="Livewire.emit('openModal', 'posts.create-post-modal')" class="btn btn-primary">Create New Post</a>
            </div>
        </div>
    </x-slot>

    <div class="container">
        <div class="row">
            <!-- Blog entries-->
            @livewire('posts.show')
            <div class="col-lg-3">
                <!-- Search widget-->
                @livewire('posts.search')
                <!-- Categories widget-->
                @livewire('posts.categories-widget')
                <!-- Side widget-->
                <div class="card mb-4">
                    <div class="card-header">Side Widget</div>
                    <div class="card-body">You can put anything you want inside of these side widgets. They are easy to use, and feature the Bootstrap 5 card
                        component!</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
