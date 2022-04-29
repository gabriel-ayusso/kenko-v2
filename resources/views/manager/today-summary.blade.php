@extends('layouts.app')

@php

$today = Carbon\Carbon::parse('today');

@endphp

@section('content')

<h2>Visão dos agendamentos de hoje ({{$today->format('d/m/Y')}})</h2>
<form action="{{route('manager.invoice')}}">
    @csrf
    <div class="card">
        <div class="card-header">Serviços agendados para hoje <button type="submit" class="btn btn-sm btn-outline-primary float-right"><i class="fas fa-receipt"></i> Nota</button> </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-sm">
                    <tr>
                        <th>Data</th>
                        <th>Profissional</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                    </tr>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="checkbox" name="bookings[]" id="bookings" value="{{$booking->id}}">
                                <a href="{{route('bookings.show', $booking)}}">{{$booking->date->format('d/m/Y H:i')}}</a>
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
                            </label>
                        </td>
                        <td>{{$booking->employee->firstname . ' ' . $booking->employee->lastname}}</td>
                        <td>{{$booking->name}}</td>
                        <td>{{$booking->service->name}}</td>
                    </tr>
                    @endforeach
                </table>

            </div>
        </div>
    </div>
</form>

@endsection
