{{-- Port of ToolMarquee.tsx with its section from page.tsx. The motion lives in resources/js/motions/colophon.js. --}}
@php
    $tools = array_values(array_filter(array_map('trim', explode('·', $data['tools'] ?? ''))));
@endphp
{{-- The band runs edge to edge, the label stays in the margin. --}}
<section class="pt-[96px]">
    <div class="gut">
        <span class="lbl text-ink-dimmer"@textMotion('label')>{{ $data['label'] ?? '' }}</span>
    </div>
    <div class="relative mt-[16px] overflow-hidden border-y border-hair py-[26px] motion-reduce:overflow-visible motion-reduce:px-[var(--gutter)]">
        <div data-motion="colophon" class="flex w-max motion-reduce:w-full">
            @foreach ([false, true] as $copy)
                {{-- Without motion the list wraps instead, and the copy goes away. --}}
                <div{!! $copy ? ' aria-hidden="true"' : '' !!} class="flex shrink-0 items-center gap-[26px] pr-[26px] {{ $copy ? 'motion-reduce:hidden' : 'motion-reduce:w-full motion-reduce:shrink motion-reduce:flex-wrap motion-reduce:gap-y-[10px]' }}">
                    @foreach ($tools as $tool)
                        <span class="flex shrink-0 items-center gap-[26px]">
                            <span class="text-[clamp(22px,3.2vw,34px)] leading-none font-medium tracking-[-0.03em] whitespace-nowrap">{{ $tool }}</span>
                            <span aria-hidden="true" class="block size-[5px] shrink-0 rounded-full bg-accent"></span>
                        </span>
                    @endforeach
                </div>
            @endforeach
        </div>
        {{-- The edges fade into the paper so the band does not stop abruptly. --}}
        <span aria-hidden="true" class="pointer-events-none absolute inset-y-0 left-0 w-[90px] bg-[linear-gradient(to_right,var(--paper),transparent)] motion-reduce:hidden"></span>
        <span aria-hidden="true" class="pointer-events-none absolute inset-y-0 right-0 w-[90px] bg-[linear-gradient(to_left,var(--paper),transparent)] motion-reduce:hidden"></span>
    </div>
</section>
