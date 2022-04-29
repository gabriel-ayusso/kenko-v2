@extends('layouts.app')

@php

function getAvailability($a)
{
switch($a)
{
case 7: return 'Domingo';
case 1: return 'Segunda';
case 2: return 'Terça';
case 3: return 'Quarta';
case 4: return 'Quinta';
case 5: return 'Sexta';
case 6: return 'Sábado';
default: return '-';
}
}

@endphp

@section('content')

<div class="row mb-2">
    <div class="col-md-5">
        <h1>{{$employee->firstname}}</h1>
    </div>
    <div class="col-md-2 clearfix">
        <a href="{{route('availabilities.create', $employee)}}" class="btn btn-primary float-right ml-2">Novo</a>
        <a href="{{route('employees.welcome')}}" class="btn btn-outline-primary float-right">Voltar</a>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Horários <strong>disponíveis</strong></div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>Dia</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th></th>
                    </tr>
                    @foreach($availabilities as $availability)
                    <tr>
                        <td>{{getAvailability($availability->weekday)}}</td>
                        <td>{{$availability->start->format('H:i')}}</td>
                        <td>{{$availability->end->format('H:i')}}</td>
                        <td class="text-right">
                            <form method="POST" action="{{route('availabilities.destroy', ['employee' => $employee, 'availability' => $availability])}}">
                                @csrf
                                @method('DELETE')
                                <a href="{{route('availabilities.edit', ['employee' => $employee, 'availability' => $availability])}}" class="btn btn-sm btn-primary">Editar</a>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir essa disponibilidade?')">excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>

    </div>
</div>
@endsection