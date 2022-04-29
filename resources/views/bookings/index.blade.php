@extends('layouts.app')

@php
$fullwidth = true;
@endphp

@section("content")
<noscript>Você precisa habilitar javascript para rodar essa aplicação.</noscript>
<div id="app"></div>


<div class="toast" style="position: absolute; top: 50px; right: 20px;" data-delay="10000" id="toast-booking">
    <div class="toast-header">
        <strong class="mr-auto" id="toast-header"></strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body" id="toast-body"></div>
</div>
@endsection

@section('scripts')
<script src="{{asset('js/app-booking.js')}}" defer></script>
@endsection