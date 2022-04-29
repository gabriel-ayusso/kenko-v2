@extends('layouts.app')

@section('content')
<h1>Novo Ciclo de Faturamento</h1>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('cycles.store')}}">
                @csrf

                @textbox(['name' => 'start', 'label' => 'Início', 'type' => 'date', 'required' => true])
                @textbox(['name' => 'end', 'label' => 'Fim', 'type' => 'date'])

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <a href="{{route('cycles.index')}}" class="btn btn-outline-primary ml-2">Voltar</a>
                        <button type="submit" class="btn btn-primary ml-2">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection