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
    {{-- Runs while the HTML is parsed, before first paint, so dark mode never flashes light. From ThemeScript.tsx. --}}
    <script>(function(){try{var t=localStorage.getItem("theme");if(!t){t=matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light"}document.documentElement.dataset.theme=t}catch(e){}})()</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans">
    <x-site-header />
    <main>{{ $slot }}</main>
    <x-site-footer />
    @livewireScripts
</body>
</html>
