<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Detect Face</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <App/>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.0.0"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface@0.0.5"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm@2.0.0/dist/tf-backend-wasm.js"></script>
        <script src="{{ asset('/js/app.js') }}"></script>        
    </body>
</html>
