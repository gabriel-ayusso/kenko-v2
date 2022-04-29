@extends('layouts.app')

@php

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
    <h1>Visão Semanal - Funcionário</h1>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    @php $current = $start->clone(); @endphp
                    @for ($i = 0; $i < 7; $i++)
                        <th class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                            {{$current->format('d')}} <br/>
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
                    $currentTime = \Carbon\Carbon::parse($minmax->min);
                    $maxTime = \Carbon\Carbon::parse($minmax->max);
                @endphp

                @while($currentTime < $maxTime)
                    <tr>
                        <td>{{$currentTime->format('H:i')}}</td>

                        @php
                            $current = $start->clone();
                            $startDate = \Carbon\Carbon::create($start->year, $start->month, $start->day, $currentTime->hour, $currentTime->minute, $currentTime->second);
                            $endDate = $startDate->clone()->addHour();
                        @endphp

                        @for ($i = 0; $i < 7; $i++)
                            <td class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                                @foreach($bookings as $booking)
                                    @if($booking->date >= $startDate && $booking->date < $endDate)
                                        <span title="{{$booking->service->name}}" class="appointment badge {{status($booking)}}"><a href="{{route('bookings.show', $booking)}}"><strong>[{{$booking->date->format('H:i')}}]</strong> {{$booking->name}}</span></a>
                                    @endif
                                @endforeach
                            </td>
                            @php
                                $current->addDay();
                                $startDate->addDay();
                                $endDate = $startDate->clone()->addHour();
                            @endphp
                        @endfor
                    </tr>

                    @php
                        $currentTime->addHour();
                    @endphp
                @endwhile
            </tbody>
        </table>
    </div>


@endsection

@section('head')
<style>
    .weekend { background-color: #ddd; }
    .today { background-color: #b8fcd1; }
    .appointment {
        text-transform: capitalize;
        white-space: normal;
        display: block;
        margin-bottom: 2px;
        text-align: left;
        line-height: 1.3;
    }
    .badge a { color: #fff; }
    .bg-info a { color: #212529; }
    @media (min-width: 576px ) {
        table { table-layout: fixed; }
    }
</style>
@endsection
