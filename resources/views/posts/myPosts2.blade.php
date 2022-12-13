<x-app-layout>
    <x-slot name="header">
        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Posts') }}
            </h2>
            @livewire('posts.all-posts-button')
            @livewire('posts.my-posts-button')
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