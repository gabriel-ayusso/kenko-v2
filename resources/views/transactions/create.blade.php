@extends('layouts.app')

@section('content')
<h1>Nova Transação</h1>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('transactions.store')}}">
                @csrf

                @textbox(['name' => 'date', 'type' => 'datetime-local', 'label' => 'Data', 'required' => true])
                @textbox(['name' => 'amount', 'type' => 'number', 'label' => 'Valor', 'required' => true])
                @textbox(['name' => 'description', 'label' => 'Descrição', 'required' => true])

                @include('bookings.bookingfinder')

                <div class="form-group row">
                    <label for="account_cycle_id" class="col-sm-2 col-form-label">Ciclo</label>
                    <div class="col-md-10">
                        <select class="form-control" name="account_cycle_id" id="account_cycle_id">
                            @foreach($cycles as $cycle)
                            <option value="{{$cycle->id}}" {{old('account_cycle_id') == $cycle->id ? 'selected' : ''}}>{{$cycle->start->format('d/m/Y')}}</option>
                            @endforeach
                        </select>
                        @error('account_cycle_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="employee_id" class="col-sm-2 col-form-label">Funcionário</label>
                    <div class="col-md-10">
                        <select class="form-control" name="employee_id" id="employee_id">
                            <option value="">--selecione--</option>
                            @foreach($employees as $employee)
                            <option value="{{$employee->id}}" {{old('employee_id') == $employee->id ? 'selected' : ''}}>{{$employee->firstname}} {{$employee->lastname}}</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="service_id" class="col-sm-2 col-form-label">Serviço</label>
                    <div class="col-md-10">
                        <select class="form-control" name="service_id" id="service_id">
                            <option value="">--selecione--</option>
                            @foreach($services as $service)
                            <option value="{{$service->id}}" {{old('service_id') == $service->id ? 'selected' : ''}}>[{{$service->category}}] {{$service->name}}</option>
                            @endforeach
                        </select>
                        @error('service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <a href="{{route('transactions.index')}}" class="btn btn-outline-primary me-2">Voltar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
