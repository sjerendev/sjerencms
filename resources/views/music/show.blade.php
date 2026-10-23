{{-- Slapp.dc.html from lg, SlappMobil.dc.html below. --}}
@php
    $playlist = $release->playlist();
    $first = $release->tracks->first();
    $line = __('music.trackLine', ['n' => ':n', 'time' => ':time']);
@endphp
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut pt-[12px] lg:pt-[36px]">
        <a href="{{ route('music.index', ['alias' => $release->aliasSlug()]) }}" wire:navigate class="lbl inline-block py-[10px] text-ink-dim hover:text-accent lg:py-0">
            ← {{ __('music.back') }}<span class="hidden lg:inline"> / {{ $release->artist }}</span>
        </a>
    </div>

    <div class="gut flex flex-col gap-[22px] pt-[8px] lg:grid lg:grid-cols-[600px_minmax(0,1fr)] lg:items-start lg:gap-[64px] lg:pt-[36px]">
        @if ($cover = $release->coverUrl())
            <img src="{{ $cover }}" alt="{{ __('music.coverAlt', ['title' => $release->title]) }}" class="block aspect-square w-full object-cover lg:size-[600px]">
        @else
            <span role="img" aria-label="{{ __('music.coverAlt', ['title' => $release->title]) }}" class="block aspect-square w-full bg-hair lg:size-[600px]"></span>
        @endif

        <div class="flex min-w-0 flex-col gap-[22px] lg:gap-[32px]">
            <div class="flex flex-col gap-[10px] lg:gap-[16px]">
                <span class="lbl text-ink-dim">{{ $release->artist }}<span class="lg:hidden"> · {{ $release->released_on->year }}</span></span>
                <h1 class="m-0 text-[46px] leading-[0.98] font-semibold tracking-[-0.035em] lg:text-[96px] lg:leading-[0.95] lg:tracking-[-0.038em]">{{ $release->title }}</h1>
                <span class="text-[15px] text-ink-dim lg:hidden">{{ trans_choice('music.tracks', $release->tracks->count()) }} · {{ __('music.minutes', ['n' => $release->minutes()]) }} · WAV</span>
            </div>

            <dl class="m-0 hidden grid-cols-4 gap-[24px] border-y border-hair py-[18px] lg:grid">
                @foreach ([
                    'released' => $release->released_on->locale(app()->getLocale())->translatedFormat('j M Y'),
                    'trackCount' => $release->tracks->count(),
                    'length' => __('music.minutes', ['n' => $release->minutes()]),
                    'download' => 'WAV',
                ] as $key => $value)
                    <div class="flex flex-col gap-[6px]">
                        <dt class="lbl text-ink-dimmer">{{ __('music.'.$key) }}</dt>
                        <dd class="m-0 text-[16px]">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($playlist)
                {{-- The whole album as one line, one segment per track, width = duration. --}}
                <div data-scrub-for="{{ $release->slug }}" data-line="{{ $line }}" class="hidden flex-col gap-[12px] lg:flex">
                    <div class="flex items-center gap-[18px]">
                        <x-play-button :release="$release" :icon="18" class="size-[64px] bg-bar-sound text-plate" />
                        <div class="flex flex-col gap-[4px]">
                            <span data-scrub-title class="text-[20px] font-semibold tracking-[-0.01em]">{{ $first->title }}</span>
                            <span data-scrub-line class="lbl text-ink-dim">{{ __('music.trackLine', ['n' => $first->position, 'time' => '0:00 / '.$first->clock()]) }}</span>
                        </div>
                    </div>
                    <div data-scrub="{{ $release->slug }}" class="flex h-[30px] items-center gap-[4px]">
                        @foreach ($release->tracks as $track)
                            <div data-scrub-track="{{ $track->id }}" style="flex: {{ $track->duration_seconds }} 1 0" @class(['relative flex h-[30px] items-center', 'cursor-pointer' => $track->previewUrl()])>
                                <span class="block h-[2px] w-full bg-hair"></span>
                                <span data-scrub-progress style="width: 0%" class="absolute inset-y-0 left-0 flex items-center overflow-hidden bg-paper">
                                    <svg width="100%" height="30" viewBox="0 0 200 30" preserveAspectRatio="none" aria-hidden="true"><path d="M0 15 Q 6 3 12 15 T 24 15 T 36 15 T 48 15 T 60 15 T 72 15 T 84 15 T 96 15 T 108 15 T 120 15 T 132 15 T 144 15 T 156 15 T 168 15 T 180 15 T 192 15 T 200 15" fill="none" stroke="var(--bar-sound)" stroke-width="1.8"/></svg>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-[14px] lg:hidden">
                    <x-play-button :release="$release" :icon="16" class="size-[56px] bg-bar-sound text-plate" />
                    <span class="lbl">{{ __('music.playAll') }}</span>
                </div>
            @endif

            <ol class="m-0 list-none p-0">
                @foreach ($release->tracks as $track)
                    <li class="data-current:text-bar-sound grid min-h-[52px] grid-cols-[28px_minmax(0,1fr)_44px_72px] items-center gap-[10px] border-t border-hair text-[16px] last:border-b lg:grid-cols-[44px_36px_minmax(0,1fr)_70px_130px] lg:gap-[16px] lg:py-[14px] lg:text-[17px]">
                        <span class="lbl text-ink-dimmer">{{ sprintf('%02d', $track->position) }}</span>
                        @if ($track->previewUrl())
                            <button
                                type="button"
                                data-play-track="{{ $track->id }}"
                                data-release="{{ $release->slug }}"
                                data-label-play="{{ __('music.play', ['title' => $track->title]) }}"
                                data-label-pause="{{ __('music.pause', ['title' => $track->title]) }}"
                                aria-label="{{ __('music.play', ['title' => $track->title]) }}"
                                aria-pressed="false"
                                class="hidden size-[36px] cursor-pointer items-center justify-center rounded-pill border border-sound text-sound lg:flex"
                            ><svg width="11" height="11" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 1.5 12 7 3 12.5Z" fill="currentColor"/></svg></button>
                        @else
                            <span class="hidden lg:block"></span>
                        @endif
                        <span>{{ $track->title }}</span>
                        <span class="text-right text-ink-dim">{{ $track->clock() }}</span>
                        <button
                            type="button"
                            data-cart-add="track"
                            data-id="{{ $track->id }}"
                            data-amount="{{ $track->min_price }}"
                            class="lbl inline-flex h-[40px] cursor-pointer items-center justify-center rounded-pill border border-buy text-buy hover:border-accent hover:bg-accent hover:text-plate"
                        ><span class="lg:hidden">{{ $track->min_price }} kr</span><span class="hidden lg:inline">{{ __('music.buy') }} · {{ $track->min_price }} kr</span></button>
                    </li>
                @endforeach
            </ol>

            {{-- Name your price. Not submitted: the cart (phase 4) checks the amount on the server. --}}
            <form onsubmit="event.preventDefault()" class="-mx-[var(--gutter)] flex flex-col gap-[14px] border-y border-panel-line bg-panel px-[20px] py-[22px] text-[#edecea] lg:mx-0 lg:gap-[16px] lg:border lg:p-[28px]">
                <div class="flex items-baseline justify-between">
                    <span class="lbl">{{ __('music.buyAlbum') }}</span>
                    <span class="lbl text-[rgba(237,236,234,0.66)]">{{ __('music.inWav', ['count' => $release->tracks->count()]) }}</span>
                </div>
                <div class="flex flex-col gap-[14px] lg:flex-row lg:items-stretch lg:gap-[12px]">
                    <label class="flex grow items-center gap-[10px] border border-[rgba(237,236,234,0.55)] px-[16px] lg:px-[18px]">
                        <span class="lbl whitespace-nowrap text-[rgba(237,236,234,0.66)]">
                            <span class="lg:hidden">{{ __('music.atLeast', ['min' => $release->min_price]) }}</span>
                            <span class="hidden lg:inline">{{ __('music.youChoose', ['min' => $release->min_price]) }}</span>
                        </span>
                        <input
                            type="number"
                            inputmode="numeric"
                            min="{{ $release->min_price }}"
                            value="{{ $release->min_price }}"
                            aria-label="{{ __('music.amount') }}"
                            data-amount-input
                            class="min-w-0 grow border-0 bg-transparent py-[12px] text-right text-[24px] font-semibold text-[#edecea] lg:text-[28px]"
                        >
                        <span class="text-[18px] lg:text-[20px]">kr</span>
                    </label>
                    <button
                        type="button"
                        data-cart-add="release"
                        data-id="{{ $release->id }}"
                        data-amount="{{ $release->min_price }}"
                        class="lbl flex h-[52px] cursor-pointer items-center justify-center rounded-pill bg-accent px-[28px] whitespace-nowrap text-plate lg:h-auto"
                    >{{ __('music.addToCart') }}</button>
                </div>
            </form>

            <x-play-data :releases="collect([$release])->merge($more)" />

            @if (filled($release->description))
                <p class="m-0 max-w-[62ch] text-[16px] leading-[1.65] whitespace-pre-line text-ink-dim">{{ $release->description }}</p>
            @endif
        </div>
    </div>

    @if ($more->isNotEmpty())
        <section class="gut pt-[44px] lg:pt-[110px]">
            <x-section-head
                number="01"
                :label="__('music.moreFrom', ['alias' => $release->artist])"
                :aside="__('music.allCount', ['count' => $total])"
                :href="route('music.index')"
            />
            <div class="-mr-[var(--gutter)] mt-[14px] flex gap-[12px] overflow-x-auto lg:mr-0 lg:mt-[32px] lg:grid lg:grid-cols-4 lg:gap-[32px]">
                @foreach ($more as $other)
                    <a href="{{ route('music.show', ['release' => $other]) }}" wire:navigate class="flex w-[130px] shrink-0 flex-col gap-[12px] hover:text-accent lg:w-auto">
                        @if ($cover = $other->coverUrl())
                            <img src="{{ $cover }}" alt="{{ __('music.coverAlt', ['title' => $other->title]) }}" loading="lazy" class="block aspect-square w-full object-cover">
                        @else
                            <span role="img" aria-label="{{ __('music.coverAlt', ['title' => $other->title]) }}" class="block aspect-square w-full bg-hair"></span>
                        @endif
                        <span class="hidden text-[22px] leading-[1.15] font-medium tracking-[-0.02em] lg:block">{{ $other->title }}</span>
                        <span class="hidden text-[14px] text-ink-dim lg:block">{{ $other->released_on->year }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
