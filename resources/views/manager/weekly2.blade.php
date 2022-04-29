@extends('layouts.app')

@php

$fullwidth = true;
$current = $start->clone();
$today = \Carbon\Carbon::parse('today');

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

function status($status) {
    switch($status){
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

@endphp

@section('content')

<h1>Visão Semanal - Gerente</h1>

<div class="table-responsive">
    <table class="table table-bordered tb-weekly">
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

            @foreach($employees as $employee)
            @php
                $current = $start->clone();
                $limit = $current->clone()->addDay();
            @endphp
            <tr>
                <td>{{$employee[0]->employeeName}}</td>
                @php $employePrinted = true; @endphp
                @for ($i = 0; $i <= 13; $i++)
                <td class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                    @php
                    $items = array_filter($employee, function($e) use ($current, $limit) {
                        return $e->bookingDate >= $current && $e->bookingDate <= $limit;
                    });
                    @endphp

                    @foreach($items as $item)
                        <x-managerWeeklyCard :item="$item"/>
                    @endforeach
                </td>
                @php
                    $current->addDay();
                    $limit = $current->clone()->addDay();
                @endphp
                @endfor
            </tr>
            @endforeach

            @php
                $current = $start->clone();
            @endphp
            <tr>
                <th></th>
                @for ($i = 0; $i <= 13; $i++)
                    @php
                    Log::debug("is today: ", ['true' => $current == $today,'current' => $current, 'today' => $today])
                    @endphp
                    <th class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                        {{$current->format('d')}} <br />
                        {{weekday($current->dayOfWeek)}}
                        @if($current == $today)
                            <span class="badge badge-pill bg-primary">hoje</span>
                        @endif
                    </th>
                    @php $current->addDay(); @endphp
                @endfor
            </tr>

            @foreach($categories as $key => $category)
            @php
                $current = $start->clone();
                $priceDates = [];
            @endphp
            <tr class="subtotals {{$key == 'Massagem' ? 'text-bold' : ''}}">
                <td>{{$key}}</td>
                @for ($i = 0; $i <= 13; $i++)
                    @php
                    $items = array_filter($category, function($e) use ($current) {
                        return strtotime($e->date) == strtotime($current);
                    });
                    $items= array_values($items);
                    @endphp

                    @if(count($items) > 0)
                    <td>{{$items[0]->servicePrice}} ({{number_format($items[0]?->servicePrice, 0)}}%)</td>
                    @else
                    <td></td>
                    @endif

                    @php $current->addDay(); @endphp
                @endfor
            </tr>
            @endforeach

            @php $current = $start->clone(); @endphp
            <tr class="totals">
                <th></th>
                @for ($i = 0; $i <= 13; $i++)
                    @php
                        $total = 0;
                        foreach($categories as $category) {
                            foreach($category as $item) {
                                if (strtotime($item->date) == strtotime($current)) {
                                    $total += $item->servicePrice;
                                }

                            }
                        }
                    @endphp

                    <th class="{{$current->dayOfWeek === 0 ? 'weekend' : ''}}{{$current == $today ? 'today' : '' }}">
                        {{number_format($total,2)}}
                    </th>
                    @php $current->addDay(); @endphp
                @endfor
            </tr>
        </tbody>
    </table>

</div>
@endsection

@section('head')
<style>

    .weekend {
        background-color: #ddd!important;
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
        background-color: #b8fcd1!important;
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
