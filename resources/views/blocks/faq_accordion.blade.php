{{-- Port of section 04 "frågor" in om-mig/page.tsx. --}}
<section class="gut pt-[clamp(56px,8vw,96px)]">
    <x-section-head :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" />
    <div class="mt-[30px]">
        @foreach ($data['items'] ?? [] as $item)
            <div class="grid grid-cols-1 gap-[24px] border-t border-hair py-[36px] lg:grid-cols-[90px_1fr_300px] lg:gap-[56px] lg:py-[44px]">
                <span class="lbl text-ink-dimmer">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="flex flex-col gap-[20px]">
                    <h3 class="m-0 max-w-[20ch] text-[clamp(26px,4vw,42px)] leading-[1.08] font-semibold tracking-[-0.03em]">{{ $item['question'] ?? '' }}</h3>
                    <p class="m-0 max-w-[62ch] text-[17px] leading-[1.65] text-ink-dim">{{ $item['answer'] ?? '' }}</p>
                </div>
                <p class="m-0 pt-[10px] text-[15px] leading-[1.5] text-accent">{{ $item['note'] ?? '' }}</p>
            </div>
        @endforeach
        <div class="border-t border-hair"></div>
    </div>
</section>
