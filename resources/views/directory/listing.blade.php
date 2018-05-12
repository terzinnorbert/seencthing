@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-3">

        </div>
        <div class="col-9">
            <h5 class="mx-3">
                <div class="row">
                    <div class="col-md-7 col-sm-10">Name</div>
                    <div class="col-3 d-none d-md-block text-right">Last modified</div>
                    <div class="col-2 d-none d-sm-block text-right">Size</div>
                </div>
            </h5>
            <div class="directory-container list-group">
                @if ('/' !== request('path'))
                    <div class="list-group-item cursor" data-type="parent"
                         data-path="{{ url()->current().'?path='.App\Directory::generateParentPath(request('path'))}}">
                        <div class="row">
                            <div class="col-12">
                                ..
                            </div>
                        </div>
                    </div>
                @endif

                @foreach($foldersAndFiles as $folderOrFile)
                    @php
                        $icon = $type = 'file';
                    @endphp
                    @if($folderOrFile->isFolder())
                        @php
                            $icon = $type = 'folder';
                        @endphp
                    @endif

                    <div class="list-group-item cursor" data-type="{{ $type }}"
                         data-path="{{ url()->current().'?path='.$folderOrFile->getPath() }}"
                         data-id="{{ $folderOrFile->id }}">
                        <div class="row">
                            <div class="col-md-7 col-sm-10 text-truncate">
                                <i class="fas fa-{{ $icon }}"></i>
                                {{ $folderOrFile->name }}
                            </div>
                            <div class="col-3 d-none d-md-block text-right text-truncate">{{ $folderOrFile->modification_time }}</div>
                            <div class="col-2 d-none d-sm-block text-right text-truncate">{{ App\Folder::fileSize($folderOrFile->size) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <script src="{{asset('js/directory.js')}}"></script>
@endsection

