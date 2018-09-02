@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col">
                    @include('directory.breadcrumb')
                </div>
                @if (false && config('syncthing.preview'))
                    <div class="col-auto">
                        @include('directory.view')
                    </div>
                @endif
            </div>
            @if ('grid' == Cookie::get('directory_view'))
                @include('directory.grid')
                <link href="/css/jquery.fancybox.min.css" rel="stylesheet">
            @else
                @include('directory.list')
            @endif
        </div>
    </div>
    <script src="{{ mix('js/share.js') }}"></script>
@endsection

