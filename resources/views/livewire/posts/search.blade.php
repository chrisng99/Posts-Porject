<div class="card mb-4">
    <div class="card-header">Search</div>
    <div class="card-body">
        <div class="input-group">
            <input wire:model.debounce.500ms="search" wire:keydown.debounce.550ms="searchPosts" class="form-control" type="text"
                placeholder="Enter search term..." aria-label="Enter search term..." aria-describedby="button-search" />
        </div>
    </div>
</div>
