@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @foreach($folders as $folder)
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{$folder->name}}</div>

                    <div class="card-body">
                        <div class="float-right">
                            <button class="btn btn-secondary">Refresh</button>
                            <a href="/folders/{{$folder->id}}" class="btn btn-primary">Browse</a>
                        </div>
                        @php
                            $info = $folder->getStatus();
                        @endphp
                        <div class="mb-1">
                            <i class="far fa-file"></i> Files: {{ $info['globalFiles'] }}</div>
                        <div class="mb-1">
                            <i class="far fa-folder"></i> Folders: {{ $info['globalDirectories'] }}<br></div>
                        <div class="mb-1">
                            <i class="far fa-hdd"></i> Size: {{ App\Folder::fileSize($info['globalBytes']) }}<br>
                        </div>
                        <div class="mb-1">
                            <i class="far fa-clock"></i> Last
                            scan: {{ App\Client\Rest::convertTime($info['stateChanged']) }}<br></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

