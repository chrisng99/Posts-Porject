<div class="mb-4 relative flex flex-col min-w-0 break-words bg-white bg-clip-border border border-solid border-black/[0.125] rounded">
    @if (isset($header))
        <div class="py-2 px-4 mb-0 bg-black/[0.03] border-b solid border-b-black/[0.125]">
            {{ $header }}
        </div>

    @endif

    <div class="flex-auto p-4">
        {{ $slot }}
    </div>
</div>
