<div class="card mb-4">
    <div class="card-header">Categories</div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <ul class="list-unstyled mb-0">
                    @foreach ($categories as $category)
                        <li>
                            <label for="category{{ $category->id }}" class="inline-flex items-center">
                                <input wire:model="categoriesFilters" wire:click="filterPosts" id="category{{ $category->id }}" type="checkbox"
                                    value="{{ $category->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600">{{ $category->name }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
