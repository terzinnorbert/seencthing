<h5 class="mx-3">
    <div class="row">
        <div class="col-md-7 col-sm-10">@include('layouts.order',['name' => \App\Http\Controllers\DirectoryController::ORDER_NAME, 'label' => 'Name', 'order' => $order, 'folder' => $folder])</div>
        <div class="col-3 d-none d-md-block text-right">@include('layouts.order',['name' => \App\Http\Controllers\DirectoryController::ORDER_MODIFICATION, 'label' => 'Last modified', 'order' => $order, 'folder' => $folder])</div>
        <div class="col-2 d-none d-sm-block text-right">@include('layouts.order',['name' => \App\Http\Controllers\DirectoryController::ORDER_SIZE, 'label' => 'Size', 'order' => $order, 'folder' => $folder])</div>
    </div>
</h5>
<div class="directory-container list-group">
    @if ('/' !== request('path','/'))
        <div class="list-group-item cursor" data-type="parent"
             data-path="{{ url()->current().'?path='.$handler->generateParentPath(request('path'))}}">
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
             data-path="{{ url()->current().'?path=' . $handler->getDirectoryPath($folderOrFile) }}"
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
                        @if ($handler->isShareable())
                            <div class="col-auto share-container">
                                <i class="fa{{ !empty($folderOrFile->shares->count()) ? 's' : 'r' }} fa-share-square share"
                                   title="@lang('Share')"></i>
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