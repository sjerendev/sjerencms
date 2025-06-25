<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {!! $ssr['head'] ?? '' !!}
    @vite(['resources/css/app.css', 'resources/js/entry-client.js'])
</head>
<body>
    <div id="app">{!! $ssr['html'] ?? '' !!}</div>
    @if(isset($ssr['state']))
        <script>
            window.__INITIAL_STATE__ = {!! json_encode($ssr['state']) !!}
        </script>
    @endif
</body>
</html>
