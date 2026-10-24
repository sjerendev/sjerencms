@props(['title', 'description', 'alternates' => []])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach ($alternates as $language => $href)
        <link rel="alternate" hreflang="{{ $language }}" href="{{ $href }}">
    @endforeach
    {{-- Runs while the HTML is parsed, before first paint, so dark mode never flashes light. From ThemeScript.tsx,
         but light unless the visitor picked dark: the system preference is ignored (Patrik, 2026-10-24).
         data-js lets app.css hide moving text until text.js has set its start; without JavaScript it shows. --}}
    <script>(function(){try{var t=localStorage.getItem("theme");document.documentElement.dataset.js="";if(!t){t="light"}document.documentElement.dataset.theme=t}catch(e){}})()</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans">
    <x-site-header :alternates="$alternates" />
    <main>{{ $slot }}</main>
    <livewire:cart-drawer />
    <x-site-footer />
    {{-- Kept across wire:navigate, so the preview keeps playing between pages. --}}
    @persist('player')
        <x-player-bar />
    @endpersist
    @livewireScripts
</body>
</html>
