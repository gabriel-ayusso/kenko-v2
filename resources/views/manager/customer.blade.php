@extends('layouts.app')

@php

function getStatus($status) {
    switch ($status) {
        case 'A':
            return 'Agendado';
        case 'E':
            return 'Executado';
        case 'P':
            return 'Pago';
        case 'C':
            return 'Cancelado';
        default:
            return '--';
    }
}
@endphp

@section('content')
<div class="customer-page">
    <h1>Customer</h1>

    <form>
        <div class="row">
            <div class="col">
                <input type="text" id="name" name="name" placeholder="Nome" class="form-control" value="{{old('name', $filters['name']) }}">
            </div>
            <div class="col">
                <input type="text" id="phone" name="phone" placeholder="Telefone" class="form-control" value="{{old('phone', $filters['phone']) }}">
            </div>
            <div class="col">
                <input type="text" id="email" name="email" placeholder="E-mail" class="form-control" value="{{old('email', $filters['email']) }}">
            </div>
            <div class="col">
                <select id="status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="A" {{$filters['status'] == 'A' ? 'selected' : ''}}>Agendado</option>
                    <option value="E" {{$filters['status'] == 'E' ? 'selected' : ''}}>Executado</option>
                    <option value="P" {{$filters['status'] == 'P' ? 'selected' : ''}}>Pago</option>
                    <option value="C" {{$filters['status'] == 'C' ? 'selected' : ''}}>Cancelado</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <button type="button" id="btnReset" class="btn btn-outline-primary">Limpar</button>
            </div>
        </div>
    </form>

    <hr />

    @if(isset($data) && count($data) > 0)
    <h3>Relatório do cliente {{$data[0]->name}}</h3>
    <table class="table table-hover">
        <tr>
            <th>Data</th>
            <th>Nome</th>
            <th>Serviço</th>
            <th>Profissional</th>
            <th>Status</th>
            <th>Preço</th>
        </tr>

        @php
        $total = 0;
        @endphp
        @foreach($data as $item)
        @php
            if($item->status != 'C')
                $total += $item->service->price;
        @endphp
        <tr class="status-{{$item->status}}">
            <td>{{$item->date->format('d/m/Y H:i')}}</td>
            <td>
                {{$item->name}}
                @if($item->phone) <small class="text-muted badge bg-light">{{$item->phone}}</small> @endif
                @if($item->email) <small class="text-muted badge bg-light">{{$item->email}}</small> @endif
            </td>
            <td>{{$item->service->name}}</td>
            <td>{{$item->employee->firstname}} {{$item->employee->lastname}}</td>
            <td>{{getStatus($item->status)}}</td>
            <td>{{number_format($item->service->price,2)}}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="5">TOTAL</th>
            <th>{{number_format($total,2)}}</th>
        </tr>
    </table>
    @else
    <div class="alert alert-info" role="alert">Sem dados para exibir</div>
    @endif

</div>
@endsection


@section('scripts')
<script>
    $('#btnReset').on('click', (e) => {
        $('#name').val('');
        $('#phone').val('');
        $('#email').val('');
        $('#status').val('');
    });
</script>
@endsection
