@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-3 d-none d-lg-block">
            <div class="card directory-info">
                <div class="card-header">{{ $folder->label }}</div>
                <div class="card-body">
                    <h5>Devices</h5>
                    <div class="list-group">
                        @foreach($folder->getDevices() as $device)
                            <div class="list-group-item">
                                <span class="badge badge-{{ 'online' == $device['state'] ? 'success' : 'danger' }}">{{ $device['state'] }}</span>
                                {{ $device['name'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9">
            <h5 class="mx-3">
                <div class="row">
                    <div class="col-md-7 col-sm-10">Name</div>
                    <div class="col-3 d-none d-md-block text-right">Last modified</div>
                    <div class="col-2 d-none d-sm-block text-right">Size</div>
                </div>
            </h5>
            <div class="directory-container list-group">
                @if ('/' !== request('path','/'))
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
                        $type = 'file';
                        $icon = 'fa'.(App\Directory::STATE_AVAILABLE === $folderOrFile->state ? 'r' : 's' ).' fa-file';
                    @endphp
                    @if($folderOrFile->isFolder())
                        @php
                            $type = 'folder';
                            $icon = 'far fa-folder';
                        @endphp
                    @endif

                    <div class="list-group-item cursor" data-type="{{ $type }}"
                         data-path="{{ url()->current().'?path='.$folderOrFile->getPath() }}"
                         data-id="{{ $folderOrFile->id }}"
                         data-state="{{ $folderOrFile->state }}">
                        <div class="row">
                            <div class="col-md-7 col-sm-10 text-truncate">
                                <div class="row">
                                    <div class="col text-truncate">
                                        <i class="icon {{ $icon }}"></i>
                                        {{ $folderOrFile->name }}
                                    </div>
                                    <div class="col-auto">
                                        <div class="progress d-none">
                                            <div class="progress-bar progress-bar-striped" role="progressbar"
                                                 aria-valuenow="0"
                                                 aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    @if($folderOrFile->isFile())
                                        <div class="col-auto share-container">
                                            <i class="fa{{ !empty($folderOrFile->hash) ? 's' : 'r' }} fa-share-square share"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-3 d-none d-md-block text-right text-truncate">{{ $folderOrFile->modification_time }}</div>
                            <div class="col-2 d-none d-sm-block text-right text-truncate">
                                @if($folderOrFile->isFile())
                                    {{ App\Folder::fileSize($folderOrFile->size) }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="modal fade" id="share-modal" tabindex="-1" role="dialog" aria-labelledby="share-modal-label"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="share-modal-label">Share file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="share-modal-url">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="share-modal-copy">Copy</button>
                        </div>
                    </div>
                    <div id="share-modal-info" class="d-none mt-1">
                        Copied to clipboard
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('js/directory.js')}}"></script>
@endsection

