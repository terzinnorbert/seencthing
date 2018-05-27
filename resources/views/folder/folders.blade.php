@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @foreach($folders as $folder)
            <div class="col-md-8">
                <div class="card mb-2">
                    <div class="card-header">{{ $folder->label }} - {{ $folder->name }}</div>

                    <div class="card-body">
                        <div class="float-right">
                            <a href="/folders/{{ $folder->id }}/refresh" class="btn btn-secondary">Refresh</a>
                            <a href="/folders/{{ $folder->id }}" class="btn btn-primary">Browse</a>
                        </div>
                        @php
                            $info = $folder->getStatus();
                        @endphp
                        <div class="mb-1">
                            <i class="fas fa-file"></i> Files: {{ $info['globalFiles'] }}</div>
                        <div class="mb-1">
                            <i class="fas fa-folder"></i> Folders: {{ $info['globalDirectories'] }}<br></div>
                        <div class="mb-1">
                            <i class="fas fa-hdd"></i> Size: {{ App\Folder::fileSize($info['globalBytes']) }}<br>
                        </div>
                        <div class="mb-1">
                            <i class="fas fa-clock"></i> Last
                            scan: {{ App\Client\Rest::convertTime($info['stateChanged']) }}<br></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

