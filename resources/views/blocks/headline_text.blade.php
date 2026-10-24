{{-- Port of the "Om mig" section (04) in page.tsx. --}}
<section class="gut pt-[clamp(72px,10vw,116px)]">
    <x-section-head :motion="($motion ?? [])['head'] ?? null" :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" />
    <div class="mt-[44px] grid grid-cols-1 items-start gap-[72px] lg:grid-cols-[1fr_420px]">
        <h3 class="m-0 text-[clamp(28px,4vw,46px)] leading-[1.14] font-medium tracking-[-0.025em]"@textMotion('headline')>{{ $data['headline'] ?? '' }}</h3>
        <p class="m-0 pt-[8px] text-[16px] leading-[1.6] text-ink-dim"@textMotion('text')>{{ $data['text'] ?? '' }}</p>
    </div>
    @if (! empty($data['cards']))
        <div class="mt-[72px] grid grid-cols-1 gap-[56px] md:grid-cols-3">
            @foreach ($data['cards'] as $card)
                <div class="flex flex-col gap-[14px] border-t border-hair pt-[20px]">
                    <span class="lbl"@textMotion('cards_title')>{{ $card['title'] ?? '' }}</span>
                    @if (filled($card['body'] ?? null))
                        <p class="m-0 text-[15px] leading-[1.6] text-ink-dim"@textMotion('cards_body')>{{ $card['body'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>
