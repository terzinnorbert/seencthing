<div class="directory-container grid-group row">
    @if ('/' !== request('path','/'))
        <div class="grid-group-item cursor" data-type="parent"
             data-path="{{ url()->current().'?path='.App\Directory::generateParentPath(request('path'))}}">
            <div class="card">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-level-up-alt"></i>
                    </div>
                    <h5 class="card-title">Card title</h5>
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

        <div class="grid-group-item" data-type="{{ $type }}"
             data-path="{{ url()->current().'?path='.$folderOrFile->getPath() }}"
             data-id="{{ $folderOrFile->id }}"
             data-state="{{ $folderOrFile->state }}">
            <div class="card">
                @if ($folderOrFile->hasPreview())
                    <img class="card-img-top cursor"
                         style="background-image: url('{{ $folderOrFile->getPreviewUrl() }}');"
                         data-fancybox="gallery"
                         data-src="{{ $folderOrFile->getPreviewUrl() }}"
                         data-caption="{{ $folderOrFile->name }}"
                    >
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