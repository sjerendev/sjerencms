{{-- Omslag.dc.html: the aliases double as the filter. --}}
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut grid grid-cols-1 items-end gap-[40px] pt-[48px] lg:grid-cols-[minmax(0,1fr)_420px] lg:gap-[60px] lg:pt-[76px]">
        {{-- Only <br> from the language file. --}}
        <h1 class="m-0 text-[clamp(46px,7.5vw,108px)] leading-[0.95] font-semibold tracking-[-0.038em]">{!! __('music.headline') !!}</h1>
        <nav aria-label="{{ __('music.filter') }}" class="flex flex-col">
            @foreach ([['slug' => null, 'name' => __('music.everything'), 'count' => $total], ...$aliases] as $alias)
                <a
                    href="{{ route('music.index', array_filter(['alias' => $alias['slug']])) }}"
                    wire:navigate
                    @class(['flex justify-between border-t border-hair py-[11px] text-[17px] hover:text-accent', 'font-semibold' => $alias['slug'] === $active])
                    @if ($alias['slug'] === $active) aria-current="page" @endif
                ><span>{{ $alias['name'] }}</span><span class="lbl self-center text-ink-dimmer">{{ $alias['count'] }}</span></a>
            @endforeach
        </nav>
    </div>

    <section class="gut pt-[72px] lg:pt-[96px]">
        <x-section-head number="01" :label="__('music.all')" :aside="__('music.newest')" />
        <div class="mt-[32px] grid grid-cols-2 gap-x-[16px] gap-y-[32px] lg:grid-cols-4 lg:gap-x-[32px] lg:gap-y-[56px]">
            @foreach ($releases as $release)
                <x-release-card :release="$release" />
            @endforeach
        </div>
        <x-play-data :releases="$releases" />
    </section>
</x-layouts.site>
