<div class="btn-group mt-1" role="group" aria-label="View">
    @foreach([
        [
        'type' => 'grid',
        'title' => 'Grid view',
        'icon' => 'th',
        ],
        [
        'type' => 'list',
        'title' => 'List view',
        'icon' => 'list-ul',
        ]
    ] as $view)
        <a href="{{ route('directory.view',[$folder->id,$view['type']]) }}"
           class="btn btn-secondary {{ $view['type'] == Cookie::get('directory_view','list') ? 'active' : '' }}"
           title="{{ $view['title'] }}"><i class="fas fa-{{ $view['icon'] }}"></i></a>
    @endforeach
</div>