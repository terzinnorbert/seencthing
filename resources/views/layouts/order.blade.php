@php
    $direction = $name == $order[0] ? $order[1] : \App\Http\Controllers\DirectoryController::DIR_DESC;
    $href = route('directory.order', [
        'folder' => $folder->id,
        'order' => $name,
        'direction' => \App\Http\Controllers\DirectoryController::DIR_ASC == $direction ? \App\Http\Controllers\DirectoryController::DIR_DESC : \App\Http\Controllers\DirectoryController::DIR_ASC
    ]);
@endphp

<a href="{{ $href }}" class="text-dark">{{ $label }}
    @if ($name == $order[0])
        @if (\App\Http\Controllers\DirectoryController::DIR_ASC == $order[1])
            <i class="fas fa-sort-amount-up"></i>
        @else
            <i class="fas fa-sort-amount-down"></i>
        @endif
    @endif
</a>
