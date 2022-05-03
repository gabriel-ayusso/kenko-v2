@extends('layouts.app')

@section('content')

<div class="row mb-5">
    <div class="col-md-6">
        <h1>Transações</h1>
    </div>
    <div class="col-md-6 clearfix">
        <a href="{{route('transactions.create')}}" class="btn btn-primary float-right ml-2">Novo</a>
    </div>
</div>

<table class="table table-striped">
    <tr>
        <th>Data</th>
        <th>Funcionário</th>
        <th>Descrição</th>
        <th class="text-end">Valor</th>
        <th></th>
    </tr>
    @foreach($transactions as $transaction)
    <tr>
        <td>{{$transaction->date->format('d/m/Y H:i')}}</td>
        <td>@if($transaction->employee) {{$transaction->employee->firstname}} {{$transaction->employee->lastname}} @else - @endif</td>
        <td style="max-width: 250px">{{$transaction->description}}</td>
        <td class="text-end">R$ {{number_format($transaction->amount, 2, ',', '.')}}</td>
        <td class="text-end">
            <form method="POST" action="{{route('transactions.destroy', $transaction)}}">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="javascript:return confirm('Deseja realmente excluir essa transação? Essa ação não tem volta.')" class="btn btn-sm btn-danger">excluir</button>
                <a href="{{route('transactions.edit', $transaction)}}" class="btn btn-sm btn-primary">editar</a>
            </form>
        </td>
    </tr>
    @endforeach
</table>
{{$transactions->links()}}

@endsection
