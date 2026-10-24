<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    {{-- data-home lets a block adapt to the home page, e.g. the register's top spacing. --}}
    <div @if ($page->is_homepage) data-home @endif>
        {{-- A block renders only once it has a view in resources/views/blocks/. --}}
        @foreach ($page->content ?? [] as $block)
            @includeIf('blocks.'.$block['type'], ['data' => $block['data'] ?? []])
        @endforeach
    </div>
</x-layouts.site>
