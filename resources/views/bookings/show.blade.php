@extends('layouts.app')

@section('content')
<h1>Agendamento de <strong>{{$booking->service->name}}</strong></h1>
<h3 class="text-primary">{{$booking->date->format('d/m/Y H:i')}} <small class="text-muted">com {{$booking->employee->firstname . ' ' . $booking->employee->lastname}}</small></h3>
<h5>{{$booking->name}}</h5>
<h5>Telefone: {{$booking->phone}}</h5>
<h5>E-mail: <a href="mailto:{{$booking->email}}">{{$booking->email}}</a></h5>
<h5>IP: {{$booking->ip}}</h5>

@if(Auth::user()->manager)
<div class="row">
    <div class="col-md-5">
        <form method="POST" action="{{route('bookings.updateStatus', $booking)}}">
            @csrf
            @method('PATCH')
            <div class="form-group row">
                <label for="weekday" class="col-sm-2 col-form-label">Status</label>
                <div class="col-md-10">
                    <select class="form-control" name="status" id="status">
                        <option @if($booking->status === 'A') selected @endif value="A">Agendado</option>
                        <option @if($booking->status === 'E') selected @endif value="E">Executado</option>
                        <option @if($booking->status === 'P') selected @endif value="P">Pago</option>
                        <option @if($booking->status === 'C') selected @endif value="C">Cancelado</option>
                    </select>
                    @error('weekday')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 offset-md-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<button onclick="history.back()" class="btn btn-outline-primary mt-4">Voltar</button>
@endsection