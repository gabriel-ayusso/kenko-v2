@extends('layouts.app')

@section('content')
    <h1>Inserir ausência</h1>

    <div class="row">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">Preencha os campos abaixo</div>
                <div class="card-body">

                <form method="POST" action="{{route('calendars.store')}}">
                    @csrf

                    @textbox(['name' => 'start', 'label' => 'Início', 'required' => true, 'type' => 'date'])
                    @textbox(['name' => 'end', 'label' => 'Fim', 'required' => true, 'type' => 'date'])
                    @textbox(['name' => 'description', 'label' => 'Descrição'])


                    <a href="{{route('calendars.index')}}" class="btn btn-outline-primary">Voltar</a>
                    <button type="submit" class="btn btn-primary">Incluir</button>
                </form>

                </div>
            </div>

        </div>
    </div>
@endsection