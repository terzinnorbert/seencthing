<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}"/>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="/"><img src="{{ asset('logo.png') }}" class="logo"> {{ config('app.name') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            @if (Auth::user())
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('folders') }}">Folders</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="http://example.com" id="files-dropdown"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">Browse</a>
                    <div class="dropdown-menu" aria-labelledby="files-dropdown">
                        @foreach(App\Folder::all() as $folder)
                            <a class="dropdown-item"
                               href="{{ route('files',['folder' => $folder->id ]) }}">{{ $folder->name }}</a>
                        @endforeach
                    </div>
                </li>
            @endif
        </ul>
        @if (Auth::user())
            <a href="/logout" class="btn btn-warning btn-outline-warning">Logout</a>
        @endif
    </div>
</nav>
<main class="container-fluid">
    @yield('content')
</main>
</body>
</html>
