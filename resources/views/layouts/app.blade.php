<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}"/>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ mix('js/notify.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="/"><img src="{{ asset('logo.png') }}" class="logo"> {{ config('app.name') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
            aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar">
        @include('layouts.navbar')
        @if (Auth::user())
            <a href="/logout" class="btn btn-warning btn-outline-warning">Logout</a>
        @endif
    </div>
</nav>
<main class="container-fluid">
    @yield('content')
</main>
@include('layouts.notification')
</body>
</html>
