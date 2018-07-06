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
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @foreach(\App\Directory::generateBreadcrumbItems(request('path','')) as $breadcrumb)
                                @if ($breadcrumb['active'])
                                    <li class="breadcrumb-item active"
                                        aria-current="page">{{ $breadcrumb['name'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ $breadcrumb['path'] }}">{{ $breadcrumb['name'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
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
                                    <div class="col text-truncate col-name">
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
    @include('layouts.modal.share')
    <script src="{{ mix('js/directory.js') }}"></script>
@endsection

