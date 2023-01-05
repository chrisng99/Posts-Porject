<x-app-layout>
    <x-slot name="header">
        <div class="sm:flex sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categories</h2>
            <a href="{{ route('categories.create') }}">
                <x-button>Create New Category</x-button>
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-center">
                    <table class="table-fixed w-[36rem] border-separate border-spacing-4">
                        <thead class="text-xl">
                            <tr>
                                <th>Category Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="px-4 py-2 text-center">{{ $category->name }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('categories.edit', $category) }}">
                                            <x-primary-button>Edit</x-primary-button>
                                        </a>
                                        <form class="inline-block ml-1" method="POST" action="{{ route('categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')

                                            <x-danger-button onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
