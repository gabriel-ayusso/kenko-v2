@extends('layouts.app')

@php
$lastEmployee = 0;
@endphp

@section('content')
<h1>Agendamento de {{$service->name}}</h1>
<cite>{{$service->description}}</cite>

<p>Esse serviço tem tempo estimado de <strong>{{$service->time}} minutos.</strong></p>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Agendamento</div>
            <div class="card-body">
                <form method="POST" action="{{route('bookings.createStep3')}}">
                    @csrf

                    @hidden(['name' => 'service_id', 'value' => $service->id])

                    <p>Data: <strong>{{$date->isoFormat('DD/MM/YYYY')}}</strong></p>

                    <h5>Horários disponíveis:</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            @forelse($availabilities as $item)
                            @if($item->employee->id != $lastEmployee)
                                @php
                                    $lastEmployee = $item->employee->id
                                @endphp
                                <h5 class="text-primary mt-3">{{$item->employee->firstname}} {{$item->employee->lastname}}</h5>
                            @endif
                            <button type="submit" value="{{$item->time->format('Y-m-d H:i:s')}}_{{$item->employee->id}}" name="time" class="btn btn-primary mb-1">{{$item->time->format('H:i')}}</button>
                            @empty
                            <div class="alert alert-warning">
                                Sentimos muito, mas não temos horários disponíveis para esse dia. Por favor, selecione outra data.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <a href="{{route('bookings.create', ['service_id' => $service->id])}}" class="btn btn-outline-primary">Voltar</a>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection