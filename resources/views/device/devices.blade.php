@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mb-2">
        <div class="col-md-8 text-right">
            <div class="toolbar-add mb-3" style="display: none">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Device ID" aria-label="Device ID"
                           id="device-id">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="add-device">Add device</button>
                    </div>
                </div>
                <div class="add-device-error alert alert-danger d-none text-left my-2"></div>
            </div>
            <a href="#" class="btn btn-primary toolbar-add">Add device</a>
        </div>
    </div>
    <div class="row justify-content-center">
        @foreach($connections as $hash => $connection)
            <div class="col-md-8">
                <div class="card mb-2">
                    <div class="card-header">
                        @if ($connection['connected'])
                            <span class="badge badge-success">online</span>
                        @else
                            <span class="badge badge-danger">offline</span>
                        @endif
                        {{ !empty($devices[$hash]['name']) ? $devices[$hash]['name'].' - ' : '' }}{{ $hash }}
                    </div>

                    <div class="card-body">
                        <div class="mb-1">
                            <i class="fas fa-link"></i>&nbsp;Address {{$connection['address']}}
                        </div>
                        <div class="mb-1">
                            <i class="fas fa-tag"></i>&nbsp;Version {{$connection['clientVersion']}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <script src="{{asset('js/devices.js')}}"></script>
@endsection

