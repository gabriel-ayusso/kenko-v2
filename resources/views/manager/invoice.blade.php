@extends('layouts.app')

@php

$now = Carbon\Carbon::create('now');
$total = 0;

@endphp

@section('content')
<h1>Demonstrativo de Serviços Prestados</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p><strong>KENKO Studio</strong></p>
                Av. Dr. Renato de Andrade Maia, 1249<br />
                Guarulhos, São Paulo<br />
                (11) 4803-1230 | contato@kenkostudio.com.br<br />
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-right">
                <p>Nota gerada em {{$now->format('d/m/Y H:i:s')}}</p>
                <p><a href="{{env('APP_URL')}}">https://agenda.kenkostudio.com.br</a></p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <h2>Serviços</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Serviço</th>
                    <th class="text-right">Valor (R$)</th>
                </tr>
                @foreach($bookings as $booking)
                @php $total += $booking->service->price @endphp

                <tr>
                    <td>
                        <strong>{{$booking->service->name}}</strong> - {{$booking->name}}<br />
                        Profissional {{$booking->employee->firstname}} {{$booking->employee->lastname}}
                    </td>
                    <td class="text-right">
                        R$ {{number_format($booking->service->price, 2, ',', '.')}}
                    </td>
                </tr>
                @endforeach
                <tr class="table-active">
                    <td>
                        <strong>Total</strong>
                    </td>
                    <td class="text-right">
                        <strong>R$ {{number_format($total, 2, ',', '.')}}</strong>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>

@endsection