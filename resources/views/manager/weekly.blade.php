@extends('layouts.app')

@php

$fullwidth = true;
$current = $start->clone();
$today = \Carbon\Carbon::parse('today');

function status($booking) {
switch($booking->status){
case 'A':
return 'bg-info';
case 'E':
return 'bg-info';
case 'P':
return 'bg-success';
case 'C':
return 'bg-danger';
default:
return 'bg-info';
}
}

function weekday($w) {
switch($w) {
case 0: return 'Dom';
case 1: return 'Seg';
case 2: return 'Ter';
case 3: return 'Qua';
case 4: return 'Qui';
case 5: return 'Sex';
case 6: return 'Sáb';
}
}

@endphp

@section('content')
<h1>Visão Semanal - Gerente</h1>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                @for ($i = 0; $i <= 13; $i++) <th class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                    {{$current->format('d')}} <br />
                    {{weekday($current->dayOfWeek)}}
                    @if($current == $today)
                    <span class="badge badge-pill bg-primary">hoje</span>
                    @endif
                    </th>
                    @php $current->addDay(); @endphp
                    @endfor
            </tr>
        </thead>
        <tbody>
            @php
                $totalWeek = [];
                for($i=0; $i <= 13; $i++) {
                    $totalWeek[] = 0.0;
                }
                //$totalWeek = array(0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0);
            @endphp
            @foreach($employees as $employee)
            <tr>
                <td>{{$employee->firstname}}</td>
                @php
                $current = $start->clone();
                $limit = $current->clone()->addDay();
                @endphp

                @for ($i = 0; $i <= 13; $i++)
                    <td class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                        @foreach($employee->bookings()->orderBy('date')->get() as $booking)
                            @if($booking->status != 'C' && $booking->date >= $current && $booking->date <= $limit)
                                <span title="{{$booking->service->name}}" class="appointment badge {{status($booking)}}">
                                    <div href="{{route('booking.edit', $booking)}}" style="position: relative;">
                                        <span>{{$booking->date->format('H:i')}}</span> <span>{{$booking->name}}</span><br/>
                                        <small>R$ {{number_format($booking->service->price, 2, ',', '.')}}</small><br/>
                                        <div class="mt-2">
                                        <a href="{{route('booking.edit', $booking)}}"><i class="fas fa-search fa-lg"></i></a>
                                        @if($booking->status === 'A')
                                        <a href="{{route('manager.pay', $booking)}}" onclick="return confirm('Deseja marcar esse agendamento como pago?');" alt="Marcar como pago" style="position:absolute; right:0; bottom: 0;"><i class="fas fa-money-bill fa-2x"></i></a>
                                        @endif
                                        </div>
                                    </div>
                                    @php
                                        if($booking->status != 'C')
                                            $totalWeek[$i] = $totalWeek[$i] + $booking->service->price;
                                    @endphp
                                </span>
                            @endif
                        @endforeach
                    </td>
                    @php
                        $current->addDay();
                        $limit = $current->clone()->addDay();
                    @endphp
                @endfor
            </tr>
            @endforeach

            @foreach($categories as $category)
            <tr class="subtotals {{$category->name === 'Massagem' ? 'text-bold' : ''}}">
                <td>{{$category->name}}</td>
                @for($i=0; $i <= 13; $i++)
                <td class="text-end">{{number_format($category["total_".($i+1)], 2, ',', '.')}} ({{ $totalWeek[$i] == 0 ? 0 : number_format($category["total_" . ($i+1)] / $totalWeek[$i] * 100, 0) }}%)</td>
                @endfor
            </tr>
            @endforeach

            <tr class="totals">
                <td><strong>TOTAL</strong></td>
                @for($i=0; $i <= 13; $i++)
                <td class="text-end">R$ {{number_format($totalWeek[$i], 2, ',', '.')}}</td>
                @endfor
            </tr>
        </tbody>
    </table>
</div>
@endsection

@section('head')
<style>
    .weekend {
        background-color: #ddd;
    }

    .totals {
        background-color: #ddd;
        font-weight: bold;
    }

    .subtotals {
        background-color: #f5f5f5;
        line-height: 10px;
    }
    .subtotals td {
        padding: 7px;
        font-size: 0.8em;
    }

    .today {
        background-color: #b8fcd1;
    }

    .appointment {
        text-transform: capitalize;
        white-space: normal;
        display: block;
        margin-bottom: 2px;
        text-align: left;
        line-height: 1.3;
    }

    .text-bold {
        font-weight: bold;
    }

    .badge a {
        color: #fff;
    }

    .bg-info a {
        color: #212529;
    }

    @media (min-width: 576px) {
        table {
            table-layout: fixed;
        }
    }
</style>
@endsection
