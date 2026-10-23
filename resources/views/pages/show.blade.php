<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    {{-- A block renders only once it has a view in resources/views/blocks/. --}}
    @foreach ($page->content ?? [] as $block)
        @includeIf('blocks.'.$block['type'], ['data' => $block['data'] ?? []])
    @endforeach
</x-layouts.site>
