@extends('layouts.app')

@section('content')
<h1>Agendamento de {{$service->name}}</h1>
<cite>{{$service->description}}</cite>

<p>Esse serviço tem tempo estimado de <strong>{{$service->time}} minutos.</strong></p>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Agendamento com {{$employee->firstname}} {{$employee->lastname}}</div>
            <div class="card-body">
                <form method="POST" action="{{route('bookings.store')}}">
                    @csrf

                    @hidden(['name' => 'date', 'value' => $date])
                    @hidden(['name' => 'service_id', 'value' => $service->id])
                    @hidden(['name' => 'employee_id', 'value' => $employee->id])

                    <p>Agendando para: <strong>{{$date->format('d/m/Y H:i:s')}}</strong></p>

                    <p>Para finalizar, preencha o seus dados:</p>

                    @textbox(['name' => 'cpf', 'label' => 'CPF', 'required' => true])
                    @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'help' => 'Digite o seu nome completo'])
                    @textbox(['name' => 'email', 'type' => 'email', 'label' => 'Email', 'required' => true])
                    @textbox(['name' => 'phone', 'label' => 'Telefone', 'required' => true])

                    <button type="submit" class="btn btn-primary">Agendar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection