@extends('layouts.app')

@section('content')
<h1>Nova disponibilidade para {{$employee->firstname}}</h1>

<div class="card">
    <div class="card-header">Por favor preencha os campos abaixo</div>

    <div class="card-body">
        <form method="POST" action="{{route('unavailabilities.update', ['employee' => $employee, 'unavailability' => $unavailability])}}">
            @csrf
            @method('PUT')

            @textbox(['name' => 'start', 'label' => 'Início', 'type' => 'datetime-local', 'value' => old('start', $unavailability->start->format('Y-m-d\TH:i:s')) ])
            @textbox(['name' => 'end', 'label' => 'Fim', 'type' => 'datetime-local', 'value' => old('end', $unavailability->end->format('Y-m-d\TH:i:s'))])
            @textbox(['name' => 'description', 'label' => 'Descrição', 'value' => old('description', $unavailability->description)])

            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <a href="{{route('unavailabilities.index', $employee)}}" class="btn btn-outline-primary">Voltar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection