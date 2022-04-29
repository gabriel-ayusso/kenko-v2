@extends('layouts.app')

@section('content')
<div class="row mb-5">
    <div class="col-md-6">
        <h1>Agendamentos Registrados</h1>
    </div>
    <div class="col-md-6 clearfix">
        <a href="{{route('booking.create')}}" class="btn btn-primary float-right ml-2">Novo</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-sm">
        <tr>
            <th>Data</th>
            <th>Cliente</th>
            <th>Profissional</th>
            <th>Serviço</th>
            <th></th>
        </tr>
        @foreach($bookings as $booking)
        <tr>
            <td>
                {{$booking->date->format('d/m/Y H:i')}}
                @switch($booking->status)
                    @case('A')
                        <span class="badge bg-primary">agendado</span>
                        @break
                    @case('E')
                        <span class="badge bg-warning">executado</span>
                        @break
                    @case('P')
                        <span class="badge bg-success">pago</span>
                        @break
                    @case('C')
                        <span class="badge bg-danger">cancelado</span>
                        @break
                    @default
                        @break
                @endswitch
            </td>
            <td>{{$booking->name}}</td>
            <td>{{$booking->employee->firstname}} {{$booking->employee->lastname}}</td>
            <td>{{$booking->service->name}}</td>
            <td class="text-right">
                <form method="POST" action="{{route('booking.destroy', $booking)}}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="javascript:return confirm('Deseja realmente excluir esse agendamento? Essa ação não tem volta.')" class="btn btn-sm btn-outline-danger">excluir</button>
                    <a href="{{route('booking.edit', $booking)}}" class="btn btn-sm btn-outline-primary">editar</a>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
{{$bookings->links()}}

@endsection
