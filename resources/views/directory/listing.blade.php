@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-3">

        </div>
        <div class="col-9">
            <h5>
                <div class="row">
                    <div class="col-md-7 col-sm-10">Name</div>
                    <div class="col-3 d-none d-md-block text-right">Last modified</div>
                    <div class="col-2 d-none d-sm-block text-right">Size</div>
                </div>
            </h5>
            @foreach($foldersAndFiles as $folderOrFile)
                <div class="row py-2 border">
                    <div class="col-md-7 col-sm-10 text-truncate">
                        @if($folderOrFile->isFolder())
                            <i class="fas fa-folder"></i>
                        @else
                            <i class="fas fa-file"></i>
                        @endif
                        {{ $folderOrFile->name }}
                    </div>
                    <div class="col-3 d-none d-md-block text-right text-truncate">{{ $folderOrFile->modification_time }}</div>
                    <div class="col-2 d-none d-sm-block text-right text-truncate">{{ App\Folder::fileSize($folderOrFile->size) }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

