@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-md-center" style="height: 80vh">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">Available peers:</div>
                    <div class="col text-right">{{ $peers['available'] }}/{{ $peers['all'] }}</div>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title text-center text-muted">{{ $directory->name }}</h5>
                <p class="card-text text-center">
                    @if ($peers['available'])
                        <a href="{{url()->current()}}/download" id="download" class="btn btn-primary" target="_blank">
                            Download
                        </a>
                @else
                    <div class="alert alert-warning text-center">
                        There are no available peers for download :(<br>
                        Check back later
                    </div>
                    @endif
                    </p>
            </div>
            @if ($peers['available'])
                <div class="card-footer">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%;"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <script src="{{ mix('js/share.js') }}"></script>
@endsection