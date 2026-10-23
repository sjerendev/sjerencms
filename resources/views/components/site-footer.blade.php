{{-- Port of Footer.tsx. The links come from the site settings; a row without a value is left out. --}}
@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $links = array_filter([
        'email' => $settings->email ? 'mailto:'.$settings->email : null,
        'linkedin' => $settings->linkedin_url,
        'facebook' => $settings->facebook_url,
        'youtube' => $settings->youtube_url,
        'bandcamp' => $settings->bandcamp_url,
    ]);
@endphp
<footer class="gut pt-[116px] pb-[40px]">
    <div class="grid grid-cols-1 items-start gap-[56px] border-t border-hair-strong pt-[52px] lg:grid-cols-[1fr_340px]">
        <h2 class="m-0 max-w-[14ch] text-[clamp(38px,6vw,76px)] leading-[0.98] font-semibold tracking-[-0.035em]">
            {{ __('footer.headline') }}
            {{-- Own line: otherwise the last word of the question ends up alone. --}}
            <span class="block text-accent">{{ __('footer.headlineAccent') }}</span>
        </h2>
        <div class="flex flex-col">
            @foreach ($links as $key => $href)
                <a
                    href="{{ $href }}"
                    class="wave-link flex justify-between border-b border-hair-mid py-[15px] text-[17px] hover:text-accent"
                    @if (str_starts_with($href, 'http')) target="_blank" rel="noreferrer" @endif
                >
                    {{ $key === 'email' ? $settings->email : __('footer.'.$key) }} <span aria-hidden="true">↗</span>
                    <x-wave-underline />
                </a>
            @endforeach
        </div>
    </div>
    <div class="mt-[72px] flex flex-wrap justify-between gap-3">
        <span class="lbl text-ink-dimmer">{{ __('footer.copyright', ['year' => now()->year]) }}</span>
        <span class="lbl text-ink-dimmer">{{ __('footer.place') }}</span>
    </div>
</footer>
