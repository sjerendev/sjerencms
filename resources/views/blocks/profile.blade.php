{{-- Port of section 01 "profil" in om-mig/page.tsx. Paragraphs are separated by a blank line. --}}
@php
    $paragraphs = fn (?string $text) => array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/u', $text ?? '')), fn ($p) => $p !== ''));
@endphp
<section class="gut pt-[clamp(56px,8vw,90px)]">
    <x-section-head :number="$data['number'] ?? ''" :label="$data['label'] ?? ''" />
    <div class="mt-[44px] grid grid-cols-1 items-start gap-[56px] md:grid-cols-2 lg:grid-cols-[1fr_1fr_260px]">
        <div class="flex flex-col gap-[22px]">
            @foreach ($paragraphs($data['column_one'] ?? null) as $paragraph)
                @if ($loop->first)
                    <p class="m-0 text-[17px] leading-[1.65]">{{ $paragraph }}</p>
                @else
                    <p class="m-0 text-[16px] leading-[1.65] text-ink-dim">{{ $paragraph }}</p>
                @endif
            @endforeach
        </div>
        <div class="flex flex-col gap-[22px]">
            @foreach ($paragraphs($data['column_two'] ?? null) as $paragraph)
                <p class="m-0 text-[16px] leading-[1.65] text-ink-dim">{{ $paragraph }}</p>
            @endforeach
        </div>
        <div class="flex flex-row flex-wrap gap-[26px] pt-[6px] lg:flex-col">
            @foreach ($data['stats'] ?? [] as $stat)
                <div class="flex flex-1 flex-col gap-[5px] border-t border-hair pt-[16px]">
                    <span class="text-[40px] font-semibold tracking-[-0.03em]">{{ $stat['value'] ?? '' }}</span>
                    <span class="lbl text-ink-dim">{{ $stat['label'] ?? '' }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
