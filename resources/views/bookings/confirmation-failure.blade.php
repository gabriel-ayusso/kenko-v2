@extends('layouts.app')

@section('content')
    <h1 class="text-danger">Ops, houve um problema.</h1>

    <p>Sentimos muito pelo inconveniente, mas a confirmação não pôde ser realizada.</p>

    <div class="alert alert-danger">
        {{$message}}
    </div>

    <p>Por favor, entre em contato conosco para verificarmos o que houve.</p>
@endsection