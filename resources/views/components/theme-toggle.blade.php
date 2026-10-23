{{-- Port of ThemeToggle.tsx. resources/js/theme.js sets aria-pressed and data-on. --}}
<div
    class="flex items-center gap-[5px] rounded-pill border border-hair-mid p-[4px_5px]"
    role="group"
    aria-label="{{ __('nav.theme') }}"
>
    <button
        type="button"
        data-theme-pick="light"
        aria-label="{{ __('nav.themeLight') }}"
        aria-pressed="false"
        class="grid size-[21px] cursor-pointer place-items-center rounded-pill border-0 bg-transparent p-0 text-ink-dimmer transition-colors data-[on=true]:bg-ink data-[on=true]:text-paper"
    >
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <circle cx="12" cy="12" r="4.2" />
            <path d="M12 2.4v2.2M12 19.4v2.2M2.4 12h2.2M19.4 12h2.2M5.2 5.2l1.6 1.6M17.2 17.2l1.6 1.6M18.8 5.2l-1.6 1.6M6.8 17.2l-1.6 1.6" />
        </svg>
    </button>
    <button
        type="button"
        data-theme-pick="dark"
        aria-label="{{ __('nav.themeDark') }}"
        aria-pressed="false"
        class="grid size-[21px] cursor-pointer place-items-center rounded-pill border-0 bg-transparent p-0 text-ink-dimmer transition-colors data-[on=true]:bg-ink data-[on=true]:text-paper"
    >
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z" />
        </svg>
    </button>
</div>
