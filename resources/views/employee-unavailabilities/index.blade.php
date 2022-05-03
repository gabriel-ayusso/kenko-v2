@extends('layouts.app')

@section('content')
    <div class="row mb-2">
        <div class="col-md-7">
            <h1>{{$employee->firstname}}</h1>
        </div>
        <div class="col-md-3 clearfix">
            <a href="{{route('unavailabilities.create', $employee)}}" class="btn btn-primary float-right ml-2">Novo</a>
            <a href="{{route('employees.welcome')}}" class="btn btn-outline-primary float-right ml-2">Voltar</a>
        </div>
    </div>


    <div class="row">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Ausências cadastradas</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Descrição</th>
                            <th></th>
                        </tr>
                        @foreach($unavailabilities as $unavailability)
                        <tr>
                            <td>{{$unavailability->start->format('d/m/Y H:i')}}</td>
                            <td>{{$unavailability->end->format('d/m/Y H:i')}}</td>
                            <td>{{$unavailability->description}}</td>
                            <td class="text-end">
                                <form method="POST" action="{{route('unavailabilities.destroy', ['employee' => $employee, 'unavailability' => $unavailability])}}">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{route('unavailabilities.edit', ['employee' => $employee, 'unavailability' => $unavailability])}}" class="btn btn-sm btn-primary">Editar</a>
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
