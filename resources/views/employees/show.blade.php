@extends('layouts.app')

@section('content')
<h1>{{$employee->firstname}} {{$employee->lastname}} <small class="text-muted">Funcionário</small></h1>
<h5>{{$employee->title}}</h5>

<div class="row mb-4">
    <div class="col-md-12">
        <cite>{{$employee->description}}</cite>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <table class="table">
            <tr>
                <th colspan="2">Serviços que {{$employee->firstname}} pode fazer:</th>
            </tr>
            @foreach($employee->services as $service)
            <tr>
                <td>{{$service->name}}</td>
                <td>{{$service->description}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <form method="POST" action="{{route('employees.destroy', $employee)}}">
            @csrf
            @method('DELETE')

            <a href="{{route('employees.index')}}" class="btn btn-outline-primary mr-2">Voltar</a>
            <a href="{{route('employees.edit', $employee)}}" class="btn btn-primary mr-2">Editar</a>
            <button type="submit" class="btn btn-danger" onclick="return confirm('Deseja mesmo excluir esse funcionário?')">Excluir</button>
        </form>
    </div>
</div>

@endsection