@php
    $routeName = request()->route()->getName();
@endphp

<ul class="navbar-nav mr-auto">
    @if (Auth::user())
        @foreach(['devices' => 'Devices','folders' => 'Folders'] as $item => $label)
            <li class="nav-item">
                <a class="nav-link {{ $routeName == $item ? 'active' : '' }}" href="{{ route($item) }}">{{ $label }}</a>
            </li>
        @endforeach
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ $routeName == 'files' ? 'active' : '' }}" id="files-dropdown"
               data-toggle="dropdown" href="#"
               aria-haspopup="true" aria-expanded="false">Browse</a>
            <div class="dropdown-menu" aria-labelledby="files-dropdown">
                @foreach(App\Folder::all() as $folder)
                    <a class="dropdown-item"
                       href="{{ route('files',['folder' => $folder->id ]) }}">{{ $folder->name }}</a>
                @endforeach
            </div>
        </li>
    @endif
</ul>