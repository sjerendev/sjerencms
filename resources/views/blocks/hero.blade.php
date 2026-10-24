{{-- Port of the hero section in page.tsx with HeroRotator.tsx and HeroWaves.tsx. The motions live in resources/js/motions/hero-rotator.js and hero-waves.js. --}}
@php
    $phrases = array_values(array_filter(array_map('trim', preg_split('/\R/u',$data['phrases'] ?? ''))));
    // No whitespace between the letters: inside the row it would show as a space after every one.
    $letters = fn (string $text, string $mark, string $style = '') => collect(mb_str_split($text))
        ->map(fn ($char) => '<span '.$mark.' class="inline-block whitespace-pre"'.$style.'>'.e($char).'</span>')
        ->implode('');
@endphp
<section class="gut relative isolate pt-[clamp(56px,9vw,104px)] pb-[clamp(24px,4vw,56px)]">
    {{-- Fades out both ways: up so the peaks aren't cut at the edge, down so the curves don't run over the body text. --}}
    <canvas data-motion="hero-waves" aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 size-full" style="mask-image: linear-gradient(to bottom, transparent 0%, #000 15%, #000 62%, transparent 92%); -webkit-mask-image: linear-gradient(to bottom, transparent 0%, #000 15%, #000 62%, transparent 92%)"></canvas>
    {{-- The letters are aria-hidden; the phrase is plain text for screen readers. The box is 1.35em: the line is 0.95em, the rest is room for the descenders in g and p. --}}
    <h1 class="m-0 text-[clamp(42px,8vw,108px)] leading-[0.95] font-semibold tracking-[-0.038em]">
        <span class="block text-accent"@textMotion('lead')>{{ $data['lead'] ?? '' }}</span>
        <span class="sr-only">{{ $phrases[0] ?? '' }}</span>
        <span data-motion="hero-rotator" data-phrases="{{ json_encode($phrases) }}" aria-hidden="true" class="relative block h-[1.35em] overflow-hidden">
            <span class="absolute inset-x-0 top-0 block">{!! $letters($phrases[0] ?? '', 'data-out') !!}</span>
            {{-- The next phrase waits below the edge, so without JavaScript or motion the first one stands alone.
                 An inline transform, not a translate utility: Tailwind 4 writes `translate`, which would add to GSAP's transform. --}}
            <span class="absolute inset-x-0 top-0 block">{!! $letters($phrases[1 % max(count($phrases), 1)] ?? '', 'data-in', ' style="transform: translateY(1.35em)"') !!}</span>
        </span>
    </h1>
    <div class="mt-[clamp(48px,8vw,100px)] grid grid-cols-1 items-start gap-[56px] md:grid-cols-2 lg:grid-cols-[1fr_1fr_260px]">
        <p class="m-0 text-[16px] leading-[1.6] text-ink-dim"@textMotion('intro_one')>{{ $data['intro_one'] ?? '' }}</p>
        <p class="m-0 text-[16px] leading-[1.6] text-ink-dim"@textMotion('intro_two')>{{ $data['intro_two'] ?? '' }}</p>
        <div class="flex flex-col gap-[12px]">
            <a href="{{ route('page.show', ['slug' => 'projekt']) }}" wire:navigate class="wave-link lbl flex items-center justify-between border-b border-hair-strong pb-[10px] hover:text-accent">
                {{ $data['work_label'] ?? '' }} <span aria-hidden="true">↗</span>
                <x-wave-underline />
            </a>
            <a href="{{ $data['cv_url'] ?? '' }}" class="wave-link lbl flex items-center justify-between border-b border-hair-mid pb-[10px] hover:text-accent">
                {{ $data['cv_label'] ?? '' }} <span aria-hidden="true">↓</span>
                <x-wave-underline />
            </a>
        </div>
    </div>
</section>
