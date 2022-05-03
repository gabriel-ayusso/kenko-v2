@extends('layouts.app')

@php
$fullwidth = true;
@endphp

@section('content')

<div class="row d-print-none">
    <div class="col-6">
        <h1>Resumo do Dia</h1>
    </div>
    <div class="col-6 d-flex justify-content-end align-items-center">
        <label for="date" class="me-2">Selecione a data</label>
        <input id="date" placeholder="Data" type="date" class="form-control" style="max-width: 200px;" value="{{old('dt', $dt->format('Y-m-d'))}}">
    </div>
</div>

<table class="table table-striped table-bordered next-day">
    <thead>
        <tr>
            <th colspan="12" class="text-center fs-5">Data: {{$dt->format('d/m/Y')}} - AGENDA</th>
        </tr>
        <tr>
            <th rowspan="2">Horário</th>
            <th rowspan="2">Cliente</th>
            <th rowspan="2">Profissional</th>
            <th rowspan="2">Serviço</th>
            <th rowspan="2">Valor</th>
            <th rowspan="2">Observação</th>
            <th colspan="3" class="text-center">Pagamento</th>
            <th colspan="2" class="text-center">Lançamento</th>
            <th rowspan="2">Ok Pagto C. Azul</th>
        </tr>
        <tr>
            <th>Dinheiro</th>
            <th>Cartão</th>
            <th>Pix</th>
            <th>Planilha</th>
            <th>C.Azul</th>
        </tr>
    </thead>
    <tbody>
        @foreach($services as $service)
        <tr>
            <td>{{\Carbon\Carbon::parse($service->date)->format('H:i')}}</td>
            <td>{{$service->name}}</td>
            <td>{{$service->employee}}</td>
            <td>{{$service->service}}</td>
            <td>{{number_format($service->price,2)}}</td>
            <td>{{$service->comments}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$service->ca_sale_id != null ? 'OK' : ''}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection


@section('scripts')
<script>
    $(() => {
        $('#date').bind('change', (e) => {
            window.location = `/manager/nextday?dt=${e.target.value}`
        })
    })
</script>
@endsection
