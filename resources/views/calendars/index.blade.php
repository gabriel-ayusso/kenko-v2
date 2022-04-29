@extends('layouts.app')

@section('content')
<h1>{{$employee->firstname}} {{$employee->lastname}} <small class="text-muted">Calendário</small></h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Clientes agendados <a href="#" class="btn btn-outline-secondary btn-sm float-right">Novo</a></div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Serviço</th>
                    </tr>
                    @forelse($bookings as $booking)
                        <tr>
                            <td><a href="{{route('bookings.show', $booking)}}">{{$booking->name}}</a></td>
                            <td>{{$booking->date->format('d/m/Y H:i')}}</td>
                            <td>{{$booking->service->name}}</td>
                        </tr>
                    @empty
                    @endforelse
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Ausência <a href="{{route('calendars.create')}}" class="btn btn-outline-secondary btn-sm float-right">Novo</a></div>
            <div class="card-body">
                <table class="table table-hover">
                    <tr>
                        <th>De</th>
                        <th>Até</th>
                        <th></th>
                    </tr>
                    @foreach($calendars as $calendar)
                    <tr title="{{$calendar->description}}">
                        <td>{{$calendar->start->format('d/m/Y')}}</td>
                        <td>{{$calendar->end->format('d/m/Y')}}</td>
                        <td>
                            <form method="POST" action="{{route('calendars.destroy', $calendar)}}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Deseja mesmo excluir essa ausência?')" class="btn btn-danger btn-sm float-right">excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection