{{-- Port of RegisterRow.tsx. The pointer drift (--mx, --my) is set by resources/js/register.js. --}}
@props(['project', 'number'])
@php
    $tag = $project->rebuilt ? 'div' : 'a';
    $preview = $project->previewUrl();
    $image = $project->imageUrl();
@endphp
{{-- Rebuilt sites no longer show his work, so they are not linked. --}}
<{{ $tag }} data-register-row @unless ($project->rebuilt) href="{{ $project->url }}" target="_blank" rel="noreferrer" @endunless
    class="group relative grid grid-cols-[64px_1fr] items-start gap-[20px] border-t border-hair py-[24px] hover:z-10 md:grid-cols-[minmax(360px,26%)_minmax(0,1fr)_220px] md:gap-[40px] md:py-[38px]">
    <span class="lbl text-ink-dimmer transition-colors group-hover:text-accent">/ {{ str_pad($number, 3, '0', STR_PAD_LEFT) }}</span>

    <div class="flex flex-col gap-[6px]">
        <span class="text-[15px] text-ink-dim md:text-[17px]">{{ $project->client }}</span>
        <span class="text-[19px] leading-[1.12] font-medium tracking-[-0.028em] transition-colors group-hover:text-accent md:text-[34px]">{{ $project->headline(app()->getLocale()) }}</span>
    </div>

    <div class="col-span-2 flex flex-wrap gap-x-4 gap-y-1 text-[13px] text-ink-dim md:col-span-1 md:flex-col md:gap-[4px] md:text-[14px]">
        @foreach ($project->services as $service)
            <span>{{ $service }}</span>
        @endforeach
    </div>

    {{-- The hover image. Pointer devices only; mobile gets the thumbnail below. --}}
    @if ($preview)
        <span aria-hidden="true"
            style="transform: translate(calc(var(--mx, 0) * 40px), calc(-50% + var(--my, 0) * 40px))"
            class="pointer-events-none absolute top-1/2 left-0 hidden h-[257px] w-[220px] transition-transform duration-500 ease-out [@media(hover:hover)]:md:block">
            <img src="{{ $preview }}" alt="" class="size-full rounded-[6px] -rotate-6 scale-90 object-cover object-top opacity-0 shadow-lg transition duration-300 ease-out group-hover:rotate-2 group-hover:scale-100 group-hover:opacity-100">
        </span>
    @endif

    @if ($image)
        <img src="{{ $image }}" alt="" class="col-start-1 row-start-1 mt-[22px] h-[44px] w-[64px] object-cover md:hidden">
    @endif
</{{ $tag }}>
