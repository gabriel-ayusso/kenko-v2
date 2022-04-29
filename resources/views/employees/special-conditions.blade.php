@extends('layouts.app')

@section("content")
<h2>Condições especiais de {{$employee->firstname}} {{$employee->lastname}}</h2>

<div class="card">
    <div class="card-header">
        Preencha os campos abaixo:
    </div>
    <div class="card-body">

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <table class="table">
            <tr>
                <th>Serviço</th>
                <th>Porcentagem</th>
            </tr>
            @foreach($employee->services as $service)
            <tr>
                <td>{{$service->name}}</td>
                <td>
                    <form method="POST" action="{{route('employees.special-conditions-update', $employee)}}" class="form-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="employee_id" value="{{$employee->id}}">
                        <input type="hidden" name="service_id" value="{{$service->id}}">
                        <input type="number" class="form-control mr-2" step="0.01" name="comission" value="{{old('comission',number_format($service->pivot->comission, 2))}}">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="far fa-save fa-lg"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection