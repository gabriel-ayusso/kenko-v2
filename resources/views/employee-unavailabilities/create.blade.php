@extends('layouts.app')

@section('content')
<h1>Nova disponibilidade para {{$employee->firstname}}</h1>

<div class="card">
    <div class="card-header">Por favor preencha os campos abaixo</div>

    <div class="card-body">
        <form method="POST" action="{{route('unavailabilities.store', $employee)}}">
            @csrf

            @textbox(['name' => 'start', 'label' => 'Início', 'type' => 'datetime-local'])
            @textbox(['name' => 'end', 'label' => 'Fim', 'type' => 'datetime-local'])
            @textbox(['name' => 'description', 'label' => 'Descrição'])

            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <a href="{{route('unavailabilities.index', ['employee' => $employee])}}" class="btn btn-outline-primary">Voltar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection