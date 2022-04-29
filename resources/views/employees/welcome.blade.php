@extends('layouts.app')

@php

$transactions;

$last_month = Carbon\Carbon::parse('last month');

if (isset($cycle))
{
$transactions = $employee->transactions()->where('account_cycle_id', $cycle->id)->get();
}
else {
$transactions = $employee->transactions()->whereNull('account_cycle_id')->where('date', '>=', $last_month)->get();
}



@endphp

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <h2 class="text-primary">Bem vindo(a), {{$employee->firstname}}!</h2>
    </div>
    <div class="col-md-8 text-end">
        <a href="{{route('employees.weekly', $employee)}}" class="btn btn-outline-primary float-sm-none float-md-right mb-2 ml-2"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Semana</a>
        <a href="{{route('employees.edit', $employee)}}" class="btn btn-outline-primary float-sm-none float-md-right mb-2 ml-2"><i class="fas fa-user fa-lg mr-2"></i> Perfil</a>
        <a href="{{route('unavailabilities.index', $employee)}}" class="btn btn-outline-warning float-sm-none float-md-right mb-2 ml-2"><i class="fas fa-calendar-times fa-lg mr-2"></i> Ausências</a>
        <a href="{{route('availabilities.index', $employee)}}" class="btn btn-outline-primary float-sm-none float-md-right mb-2 ml-2"><i class="fas fa-calendar-check fa-lg mr-2"></i> Horário de Trabalho</a>
        <a href="{{route('employees.booking', $employee)}}" class="btn btn-primary float-sm-none float-md-right mb-2 ml-2"><i class="fas fa-calendar-alt fa-lg mr-2"></i> Agendamento</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-2">
        @include('employees.card-next-bookings', ['employee' => $employee])
    </div>
    <div class="col-md-6">
        @include('employees.card-transactions', ['employee' => $employee, 'cycle' => $cycle])
    </div>
</div>

@endsection
