@extends('layouts.app')

@section('content')

<div class="row mb-2">
    <div class="col-md-6">
        <h1>{{$employee->firstname}}</h1>
    </div>
    <div class="col-md-2 clearfix">
        <a href="{{route('employees.welcome')}}" class="btn btn-outline-primary float-right">Voltar</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Novo Agendamento</div>
            <div class="card-body">
                <form method="POST" action="{{route('employees.booking-store', $employee)}}">
                    @csrf

                    @hidden(['name' => 'employee_id', 'value' => $employee->id])
                    @textbox(['name' => 'date', 'type' => 'date', 'label' => 'Data / Hora', 'required' => true])

                    <div class="form-group row">
                        <label for="service_id" class="col-sm-2 col-form-label">Serviço</label>
                        <div class="col-md-10">
                            <select class="form-control" name="service_id" id="service_id">
                                <option value="">--selecione--</option>
                                @foreach($employee->services as $service)
                                <option value="{{$service->id}}" {{old('service_id') == $service->id ? 'selected' : ''}}>{{$service->name}}</option>
                                @endforeach
                            </select>
                            @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="service-feedback"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Horários</label>
                        <div class="col-sm-10 {{$errors->has('time') ? ' is-invalid' : ''}}">
                            <div id="horarios"></div>
                            @error('time')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true])
                    @textbox(['name' => 'email', 'type' => 'email', 'label' => 'Email'])
                    @textbox(['name' => 'phone', 'label' => 'Telefone'])

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">Agendar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/employee-booking.js') }}" defer></script>
@endsection