{{-- Port of the hero sections in om-mig/page.tsx (large) and projekt/page.tsx (medium). --}}
@php
    $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $data['lines'] ?? '')), fn ($line) => $line !== ''));
    // Whole class strings, so Tailwind finds them.
    $heading = ($data['size'] ?? 'large') === 'medium'
        ? 'text-[clamp(38px,7vw,86px)] leading-[0.96]'
        : 'text-[clamp(42px,8vw,96px)] leading-[0.95]';
@endphp
<section class="gut pt-[clamp(48px,7vw,76px)]">
    <div class="grid grid-cols-1 items-end gap-[40px] lg:grid-cols-[1fr_420px] lg:gap-[60px]">
        <h1 class="m-0 {{ $heading }} font-semibold tracking-[-0.038em]"@textMotion('lines')>{!! implode('<br>', array_map('e', $lines)) !!}</h1>
        <p class="m-0 text-[16px] leading-[1.6] text-ink-dim"@textMotion('text')>{{ \App\Models\Project::withTotal($data['text'] ?? null) }}</p>
    </div>
</section>
