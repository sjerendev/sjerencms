{{-- Port of SectionHead.tsx: 01 ─── LABEL ........ aside --}}
@props(['number', 'label', 'aside' => null])
<div class="flex items-center gap-[20px] border-b border-hair-strong pb-[14px]">
    <span class="lbl">{{ $number }}</span>
    <span aria-hidden="true" class="block h-px w-[26px] bg-hair-strong"></span>
    <h2 class="lbl m-0 font-semibold">{{ $label }}</h2>
    @if ($aside)
        <span class="lbl ml-auto text-ink-dimmer">{{ $aside }}</span>
    @endif
</div>
