{{-- Port of LocaleSwitch.tsx. A language the page exists in links to the same page there
     (the alternates); one it does not links to that language's home page instead of a 404. --}}
@props(['alternates' => []])
@php
    $active = request()->route('locale');
    $labels = ['se' => 'SV', 'en' => 'EN'];
@endphp
<span class="lbl flex items-center gap-[6px]">
    @foreach (\App\Http\Middleware\SetLocaleFromPrefix::LOCALES as $prefix => $language)
        <span class="flex items-center gap-[6px]">
            @unless ($loop->first)
                <span class="text-ink-dimmer">/</span>
            @endunless
            <a
                href="{{ $alternates[$language] ?? url("/{$prefix}") }}"
                wire:navigate
                @isset($alternates[$language]) hreflang="{{ $language }}" @endisset
                @if ($prefix === $active) aria-current="true" @else class="text-ink-dimmer hover:text-accent" @endif
            >{{ $labels[$prefix] }}</a>
        </span>
    @endforeach
</span>
