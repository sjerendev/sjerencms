{{-- Port of LocaleSwitch.tsx. Slugs are the same in both languages, so only the prefix changes. --}}
@php
    $active = request()->route('locale');
    $rest = implode('/', array_slice(request()->segments(), 1));
    $labels = ['se' => 'SV', 'en' => 'EN'];
@endphp
<span class="lbl flex items-center gap-[6px]">
    @foreach (\App\Http\Middleware\SetLocaleFromPrefix::LOCALES as $prefix => $language)
        <span class="flex items-center gap-[6px]">
            @unless ($loop->first)
                <span class="text-ink-dimmer">/</span>
            @endunless
            <a
                href="{{ url(rtrim("/{$prefix}/{$rest}", '/')) }}"
                wire:navigate
                hreflang="{{ $language }}"
                @if ($prefix === $active) aria-current="true" @else class="text-ink-dimmer hover:text-accent" @endif
            >{{ $labels[$prefix] }}</a>
        </span>
    @endforeach
</span>
