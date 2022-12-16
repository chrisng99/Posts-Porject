<x-app-layout>
    <x-slot name="header">
        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight p-header">
                {{ __('Posts') }}
            </h2>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <a onclick="Livewire.emit('showAllPosts')" class="btn btn-primary">All Posts</a>
            </h2>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <a onclick="Livewire.emit('showMyPosts')" class="btn btn-primary">My Posts</a>
            </h2>
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
