@extends('layouts.app')

@php
$today = Carbon\Carbon::parse('today');
@endphp

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1>Gerente</h1>
        <h2>Visão geral dos funcionários</h2>
    </div>
    <div class="col-md-6 text-right">
        <form action="{{route('manager.dashboard')}}" id="cycle_form">
            <!-- <a href="{{route('manager.weekly')}}" class="btn btn-primary "><i class="far fa-calendar-check fa-lg mr-2"></i> Semana</a> -->
            <a href="{{route('manager.todaysummary')}}" class="btn btn-outline-primary ">D</a>
            <a href="{{route('manager.tomorrowsummary')}}" class="btn btn-outline-primary ">D + 1</a>
            <select name="cycle_id" id="cycle_id" class="form-control float-right ml-2" style="max-width: 250px" onchange="cycle_form.submit()">
                <option value="">-- selecione o ciclo --</option>
                @foreach($cycles as $cycle)
                <option {{$currentCycle && $currentCycle->id === $cycle->id ? 'selected' : ''}} value="{{$cycle->id}}">{{$cycle->start->format('d/m/Y')}} até {{$cycle->end ? $cycle->end->format('d/m/Y') : '-'}}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="accordion" id="accordionEmployees">
            @foreach($employees as $employee)
            <div class="accordion-item card">
                <div class="card-header" id="heading_{{$employee->id}}">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$employee->id}}" aria-expanded="false" aria-controls="collapse_{{$employee->id}}">
                            {{$employee->firstname}} {{$employee->lastname}}
                        </button>
                    </h2>
                </div>

                <div id="collapse_{{$employee->id}}" class="collapse" aria-labelledby="heading_{{$employee->id}}" data-bs-parent="#accordionEmployees">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h4>{{$employee->firstname}} {{$employee->lastname}}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                @include('employees.card-next-bookings', ['employee' => $employee])
                            </div>
                            <div class="col-md-6">
                                @include('employees.card-transactions', ['employee' => $employee, 'cycle' => $currentCycle])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

@endsection
