<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div>
                        <a class="inline-flex items-center px-4 py-2 mb-6 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                            href="{{ route('posts.create') }}">
                            Add New Post
                        </a>
                    </div>

                    <div>
                        <table style="width:auto">
                            <thead>
                                <tr>
                                    <th style="width:50%">Post Title</th>
                                    <th style="width:25%">Category</th>
                                    <th style="width:25%">Author</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($posts as $post)
                                <tr>
                                    <td class="px-4 py-2"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></td>
                                    <td style="text-align:center">{{ $post->category->name }}</td>
                                    <td style="text-align:center">{{ $post->user->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>