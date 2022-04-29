@extends('layouts.app')

@section('content')
<h1>Agendamento de {{$service->name}}</h1>
<cite>{{$service->description}}</cite>

<p>Esse serviço tem tempo estimado de <strong>{{$service->time}} minutos.</strong></p>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Agendamento</div>
            <div class="card-body">
                <form method="POST" action="{{route('bookings.createStep2')}}">
                    @csrf

                    @hidden(['name' => 'service_id', 'value' => $service->id])
                    @textbox(['name' => 'date', 'type' => 'date', 'label' => 'Data', 'required' => true, 'help' => 'Selecione o melhor dia para você.'])

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">Avançar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection