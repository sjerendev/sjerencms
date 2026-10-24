{{-- Port of SectionHead.tsx: 01 ─── LABEL ........ aside --}}
@props(['number', 'label', 'aside' => null, 'href' => null, 'motion' => null])
<div class="flex items-center gap-[20px] border-b border-hair-strong pb-[14px]"{{ \App\Support\TextMotion::attributes($motion) }}>
    <span class="lbl"{{ \App\Support\TextMotion::piece($motion) }}>{{ $number }}</span>
    <span aria-hidden="true"{{ \App\Support\TextMotion::piece($motion) }} class="block h-px w-[26px] bg-hair-strong"></span>
    <h2 class="lbl m-0 font-semibold"{{ \App\Support\TextMotion::piece($motion) }}>{{ $label }}</h2>
    @if ($aside && $href)
        <a href="{{ $href }}" wire:navigate{{ \App\Support\TextMotion::piece($motion) }} class="lbl ml-auto text-ink-dim hover:text-accent">{{ $aside }}</a>
    @elseif ($aside)
        <span class="lbl ml-auto text-ink-dimmer"{{ \App\Support\TextMotion::piece($motion) }}>{{ $aside }}</span>
    @endif
</div>
