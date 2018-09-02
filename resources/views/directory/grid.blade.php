<h5 class="mx-3">
    <div class="d-flex">
        <div class="px-2">@include('layouts.order',['name' => \App\Http\Controllers\DirectoryController::ORDER_NAME, 'label' => 'Name', 'order' => $order, 'folder' => $folder])</div>
        <div class="px-2">@include('layouts.order',['name' => \App\Http\Controllers\DirectoryController::ORDER_MODIFICATION, 'label' => 'Last modified', 'order' => $order, 'folder' => $folder])</div>
        <div class="px-2">@include('layouts.order',['name' => \App\Http\Controllers\DirectoryController::ORDER_SIZE, 'label' => 'Size', 'order' => $order, 'folder' => $folder])</div>
    </div>
</h5>
<div class="directory-container grid-group row">
    @if ('/' !== request('path','/'))
        <div class="grid-group-item cursor" data-type="parent"
             data-path="{{ url()->current().'?path=' . $handler->generateParentPath(request('path')) }}">
            <div class="card">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-level-up-alt"></i>
                    </div>
                    <h5 class="card-title">..</h5>
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

        <div class="grid-group-item {{ 'folder' == $type ? 'cursor' : '' }}" data-type="{{ $type }}"
             data-path="{{ url()->current().'?path=' . $handler->getDirectoryPath($folderOrFile) }}"
             data-id="{{ $folderOrFile->id }}"
             data-state="{{ $folderOrFile->state }}">
            <div class="card">
                @if ($folderOrFile->hasPreview())
                    <div class="card-img-top cursor lazy"
                         data-fancybox="gallery"
                         data-href="{{ $folderOrFile->getPreviewUrl() }}"
                         data-src="{{ $folderOrFile->getPreviewUrl() }}"
                         data-caption="{{ $folderOrFile->name }}"
                    ></div>
                @endif
                <div class="card-body">
                    @if (!$folderOrFile->hasPreview())
                        <div class="card-icon">
                            <i class="{{ $icon }}"></i>
                        </div>
                    @endif
                    <h5 class="card-title text-truncate">{{ $folderOrFile->name }}</h5>
                </div>
            </div>
        </div>
    @endforeach
</div>