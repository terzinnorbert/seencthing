@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-3 d-none d-lg-block">
            @include('directory.devices')
        </div>
        <div class="col-12 col-lg-9">
            <div class="row">
                <div class="col">
                    @include('directory.breadcrumb')
                </div>
                @if (config('syncthing.preview'))
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
    @include('layouts.modal.share')
    <script src="{{ mix('js/directory.js') }}"></script>
@endsection

