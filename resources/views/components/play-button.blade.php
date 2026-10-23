{{-- Plays a release from its first track; player.js turns it into pause while that release plays. --}}
@props(['release', 'icon' => 14])
<button
    type="button"
    data-play-release="{{ $release->slug }}"
    data-label-play="{{ __('music.play', ['title' => $release->title]) }}"
    data-label-pause="{{ __('music.pause', ['title' => $release->title]) }}"
    aria-label="{{ __('music.play', ['title' => $release->title]) }}"
    aria-pressed="false"
    {{ $attributes->class('flex shrink-0 cursor-pointer items-center justify-center rounded-pill') }}
><svg width="{{ $icon }}" height="{{ $icon }}" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 1.5 12 7 3 12.5Z" fill="currentColor"/></svg></button>
