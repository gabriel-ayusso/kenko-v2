@extends('layouts.app')

@section('content')

<div class="row mb-5">
    <div class="col-md-6">
        <h1>Ciclos de Faturamento</h1>
    </div>
    <div class="col-md-6 clearfix">
        <a href="{{route('cycles.create')}}" class="btn btn-primary float-right ml-2">Novo</a>
    </div>
</div>

<table class="table">
    <tr>
        <th>Início</th>
        <th>Fim</th>
        <th></th>
    </tr>
    @foreach($cycles as $cycle)
    <tr>
        <td>{{$cycle->start->format('d/m/Y')}}</td>
        <td>{{$cycle->end ? $cycle->end->format('d/m/Y'): '-'}}</td>
        <td class="text-end">
            <form method="POST" action="{{route('cycles.destroy', $cycle)}}">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="javascript:return confirm('Deseja realmente excluir esse ciclo? Essa ação não tem volta.')" class="btn btn-sm btn-danger">excluir</button>
                <a href="{{route('cycles.edit', $cycle)}}" class="btn btn-sm btn-primary">editar</a>
            </form>
        </td>
    </tr>
    @endforeach
</table>


@endsection
