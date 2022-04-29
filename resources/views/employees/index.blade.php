@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1>Funcionários</h1>
    </div>
    <div class="col-md-6 text-right"><a href="{{route('employees.create')}}" class="btn btn-primary">Novo</a></div>
</div>


<table class="table">
    <tr>
        <th>Nome</th>
        <th>Sobrenome</th>
        <th></th>
    </tr>
    @foreach($employees as $employee)
    <tr>
        <td><a href="{{route('employees.show', $employee)}}">{{$employee->firstname}}</a></td>
        <td>{{$employee->lastname}}</td>
        <td class="text-right">
            <form method="POST" action="{{route('employees.destroy', $employee)}}">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="javascript:return confirm('Deseja realmente excluir esse funcionário? Essa ação não tem volta.')" class="btn btn-sm btn-danger">excluir</button>
                <a href="{{route('employees.edit', $employee)}}" class="btn btn-sm btn-primary">editar</a>
            </form>
        </td>
    </tr>
    @endforeach
</table>


@endsection