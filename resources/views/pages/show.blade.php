<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    {{-- data-home lets a block adapt to the home page, e.g. the register's top spacing. --}}
    <div @if ($page->is_homepage) data-home @endif>
        {{-- A block renders only once it has a view in resources/views/blocks/. --}}
        {{-- $motion: the block's text motion settings, from the Swedish page on both languages (PageMotion). --}}
        @foreach ($page->content ?? [] as $block)
            @includeIf('blocks.'.$block['type'], ['data' => $block['data'] ?? [], 'motion' => $motions[$loop->index] ?? []])
        @endforeach
    </div>
</x-layouts.site>
