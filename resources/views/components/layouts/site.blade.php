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
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="font-sans">
    <main>{{ $slot }}</main>
    @livewireScripts
</body>
</html>
