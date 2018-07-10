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