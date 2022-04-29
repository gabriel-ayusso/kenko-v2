@extends('layouts.app')

@php
$total = 0;
@endphp

@section('content')

<h1>Resultado da Integração</h1>

<table class="table">
    <thead>
        <tr>
            <th></th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Profissional</th>
            <th>Serviço</th>
            <th class="text-end">Valor</th>
            <th>Resultado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $result)
        <tr class="{{$result['success'] ? '' : 'text-danger' }}">
            <td>
                @if($result['success'])
                    <i class="fa-solid fa-thumbs-up fa-lg text-success"></i>
                @else
                    <i class="fa-solid fa-thumbs-down fa-lg text-danger"></i>
                @endif
            </td>
            <td>{{$result['date']->format('d/m H:i')}}</td>
            <td>{{$result['customer']}}</td>
            <td>{{$result['employee']}}</td>
            <td>{{$result['service']}}</td>
            <td class="text-end">{{number_format($result['value'],2)}}</td>
            <td>{{$result['reason']}}</td>
        </tr>

        @php
            $total += $result['value'];
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr class="fw-bold">
            <td colspan="5">TOTAL</td>
            <td class="text-end">{{number_format($total, 2)}}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@endsection
