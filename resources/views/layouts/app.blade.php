<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="app-url" content="{{ config('app.url') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap">
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! app(\App\Settings\GeneralSettings::class)->head_code_snippets !!}
</head>
<body>
    <div id="app"></div>
    {!! app(\App\Settings\GeneralSettings::class)->footer_code_snippets !!}
</body>
</html>
