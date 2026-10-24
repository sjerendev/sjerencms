{{-- Port of section 03 "arbetar med" in om-mig/page.tsx. --}}
<section class="gut pt-[clamp(56px,8vw,96px)]">
    <x-section-head :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" />
    <div class="mt-[30px]">
        @foreach ($data['groups'] ?? [] as $group)
            <div class="grid grid-cols-1 gap-[8px] border-t border-hair py-[20px] md:grid-cols-[220px_1fr] md:gap-[40px]">
                <span class="lbl">{{ $group['label'] ?? '' }}</span>
                <span class="text-[16px] leading-[1.7] text-ink-dim">{{ $group['items'] ?? '' }}</span>
            </div>
        @endforeach
    </div>
</section>
