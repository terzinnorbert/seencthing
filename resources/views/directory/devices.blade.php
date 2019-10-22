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