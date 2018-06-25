@extends('layouts.app')

@section('content')

    <div class="row justify-content-md-center">
        <div class="col-md-8 col">
            <div class="alert alert-danger mt-3 text-center">
                {{ $error }}
            </div>
        </div>
    </div>

@endsection