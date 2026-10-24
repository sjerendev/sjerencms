{{-- Port of section 02 "erfarenhet" in om-mig/page.tsx and Timeline.tsx. The head carries a legend, so it is not <x-section-head>. --}}
@php
    $trackLabel = fn (array $entry) => ($entry['track'] ?? 'work') === 'sound' ? ($data['sound_label'] ?? '') : ($data['work_label'] ?? '');
@endphp
<section class="gut pt-[clamp(56px,8vw,96px)]">
    <div class="flex flex-wrap items-center gap-[20px] border-b border-hair-strong pb-[14px]"@textMotion('head')>
        <span class="lbl"@motionPiece('head')>{{ $data['number'] ?? '' }}</span>
        <span aria-hidden="true"@motionPiece('head') class="block h-px w-[26px] bg-hair-strong"></span>
        <h2 class="lbl m-0 font-semibold"@motionPiece('head')>{{ $data['label'] ?? '' }}</h2>
        <span class="ml-auto flex gap-[24px]"@motionPiece('head')>
            <span class="lbl flex items-center gap-[8px] text-ink-dim">
                <span aria-hidden="true" class="inline-block size-[10px] rounded-pill bg-accent"></span>
                {{ $data['work_label'] ?? '' }}
            </span>
            <span class="lbl flex items-center gap-[8px] text-ink-dim">
                <span aria-hidden="true" class="inline-block size-[10px] rounded-pill bg-mint"></span>
                {{ $data['sound_label'] ?? '' }}
            </span>
        </span>
    </div>
    <div class="mt-[26px]">
        @foreach ($data['entries'] ?? [] as $entry)
            <div class="grid grid-cols-[70px_28px_1fr] border-b border-hair py-[28px] md:grid-cols-[150px_40px_1fr_150px] md:py-[34px]">
                <span class="lbl pt-[2px] text-[13px] tracking-[0.04em] md:text-[15px]"@textMotion('entries_years')>{{ $entry['years'] ?? '' }}</span>

                <div class="relative flex justify-center">
                    <span aria-hidden="true" class="absolute top-0 -bottom-[28px] block w-px bg-hair-mid md:-bottom-[34px]"></span>
                    <span aria-hidden="true" data-track="{{ ($entry['track'] ?? 'work') === 'sound' ? 'sound' : 'work' }}" class="relative mt-[6px] block size-[11px] rounded-pill data-[track=sound]:bg-mint data-[track=work]:bg-accent"></span>
                </div>

                <div class="flex flex-col gap-[8px] pl-[18px] md:pl-[34px]">
                    <span class="text-[21px] leading-[1.14] font-semibold tracking-[-0.025em] md:text-[27px]"@textMotion('entries_title')>{{ $entry['title'] ?? '' }}</span>
                    <span class="text-[15px] text-ink-dim"@textMotion('entries_subtitle')>{{ $entry['subtitle'] ?? '' }}</span>
                    <p class="m-0 mt-[6px] max-w-[56ch] text-[16px] leading-[1.6] text-ink-dim"@textMotion('entries_body')>{{ $entry['body'] ?? '' }}</p>
                    @if (! empty($entry['tags']))
                        <div class="mt-[16px] flex flex-wrap gap-[7px]">
                            @foreach ($entry['tags'] as $tag)
                                <span class="lbl rounded-pill border border-hair-mid px-[13px] py-[6px] text-ink-dim">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    <span class="lbl mt-[12px] text-ink-dimmer md:hidden">{{ $trackLabel($entry) }}</span>
                </div>

                <span class="lbl hidden pt-[4px] text-right text-ink-dimmer md:block">{{ $trackLabel($entry) }}</span>
            </div>
        @endforeach
    </div>
</section>
