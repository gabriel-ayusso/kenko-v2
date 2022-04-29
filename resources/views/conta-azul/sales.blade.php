@extends('layouts.app')

@php
$total = 0;
@endphp

@section('content')
<div class="ca-sales">
    <div class="row">
        <div class="col-8">
            <h1>Integração com Vendas - Data <strong>{{$dt->format('d/m/Y')}}</strong></h1>
        </div>
        <div class="col-4 d-flex justify-content-end align-items-center">
            <label for="date" class="me-2">Selecione a data</label>
            <input id="date" placeholder="Data" type="date" class="form-control" style="max-width: 200px;" value="{{old('dt', $dt->format('Y-m-d'))}}">
        </div>
    </div>

    @if (isset($message))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation fa-lg text-warning"></i>
        {{$message}}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    <form method="POST" action="{{route('conta-azul.sales')}}">
        @csrf
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Profissional</th>
                    <th>Serviço</th>
                    <th class="text-end">Valor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr class="status-{{$booking->status}}">
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="id[]" value="{{$booking->id}}" {{$booking->status == 'C' ? 'disabled' : '' }} {{$booking->status == 'P' ? 'checked' : '' }} id="booking-{{$booking->id}}">
                            <label class="form-check-label" for="booking-{{$booking->id}}">
                                {{$booking->date->format('H:i')}}
                            </label>
                        </div>
                    </td>
                    <td>
                        {{$booking->name}}
                        @if($booking->phone) <small class="text-muted badge bg-light">{{$booking->phone}}</small> @endif
                        @if($booking->email) <small class="text-muted badge bg-light">{{$booking->email}}</small> @endif
                    </td>
                    <td>{{$booking->employee->firstname}} {{$booking->employee->firstname}}</td>
                    <td>{{$booking->service->name}}</td>
                    <td class="text-end">{{number_format($booking->service->price,2)}}</td>
                    <td>{{Helper::bookingStatus($booking->status)}}</td>
                </tr>
                @php $total += $booking->service->price; @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="4">Total</td>
                    <td class="text-end">{{number_format($total,2)}}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="row">
            <div class="col text-end">
                <button type="submit" class="btn btn-primary">Prosseguir</button>
            </div>
        </div>
    </form>
</div>
@endsection


@section('scripts')
<script>
    $(() => {
        $('#date').bind('change', (e) => {
            window.location = `/conta-azul/sales?dt=${e.target.value}`
        })
    })
</script>
@endsection
