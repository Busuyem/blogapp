<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Blog</title>

    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp

    @if(isset($manifest['resources/js/app.js']))
        <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/js/app.js']['css'][0] ?? '') }}">
        <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
    @endif
</head>
<body>
    <div id="app"></div>
</body>
</html>
