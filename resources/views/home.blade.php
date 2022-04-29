@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h1 class="col-md-12 text-center text-primary mt-5">Olá, {{Auth::user()->name}}!</h1>
        <h2 class="col-md-12 text-center text-primary"><i class="far fa-smile-wink fa-3x"></i></h2>
        <h2 class="col-md-12 text-center text-muted mt-2">Bem vindo ao KENKO-Studio</h2>
        <p class="col-md-12 text-center text-muted">SISTEMA DE GESTÃO DE AGENDAMENTOS</p>
    </div>
</div>
@endsection
