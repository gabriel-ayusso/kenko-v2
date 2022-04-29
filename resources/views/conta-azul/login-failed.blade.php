@extends('layouts.app')

@section('content')
<div class="ca-index">
    <div class="alert alert-danger">
        <h5>🚨 Falha no login</h5>
        <p>
            Mensagem: {{$message}}
        </p>
    </div>

    <a href="/conta-azul" class="btn btn-primary">Voltar</a>

</div>

@endsection
