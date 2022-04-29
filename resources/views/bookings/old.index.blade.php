@extends('layouts.app')

@section("content")

<h1>Agendamento</h1>

<h5>Serviços</h5>

<div class="row">
    @foreach($services as $service)
    <div class="col-md-3">
        <div class="card">
            <img src="{{route('services.avatar', $service)}}" alt="Serviço" class="card-img-top" style="padding: 20px;">
            <div class="card-body">
                <h5 class="card-title">{{$service->name}}</h5>
                <p class="card-text">{{$service->description}}</p>
                <a href="{{route('bookings.create', ['service_id' => $service->id])}}" class="btn btn-primary">R$ {{number_format($service->price, 2)}}</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection