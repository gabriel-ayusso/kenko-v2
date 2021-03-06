@extends('layouts.app')

@section('content')
<h1>Novo agendamento</h1>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle fa-lg text-warning"></i> ATENÇÃO - Esta função é específica para casos de <strong>EXCEÇÃO</strong>. <br />
    Sempre que possível, utilize as funções de agendamento que possuem todas as validações de horário.
</div>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('booking.store')}}">
                @csrf

                @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true])
                @textbox(['name' => 'email', 'label' => 'Email'])
                @textbox(['name' => 'phone', 'label' => 'Telefone'])
                @textbox(['name' => 'date', 'label' => 'Data', 'required' => true, 'type' => 'datetime-local'])

                <div class="form-group row">
                    <label for="service_id" class="col-sm-2 col-form-label">Serviço</label>
                    <div class="col-md-10">
                        <select class="form-control" name="service_id" id="service_id">
                            <option value="">--selecione--</option>
                            @foreach($services as $service)
                            <option {{$service->id == old('service_id') ? 'selected' : ''}} value="{{$service->id}}">[{{$service->category}}] {{$service->name}}</option>
                            @endforeach
                        </select>
                        @error('service_id')
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
                            <option {{$employee->id == old('employee_id') ? 'selected' : ''}} value="{{$employee->id}}">{{$employee->firstname}} {{$employee->lastname}}</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-md-10">
                        <select class="form-control" name="status" id="status">
                            <option value="">--selecione--</option>
                            <option {{old('status') == 'A' ? 'selected' : ''}} value="A">Agendado</option>
                            <option {{old('status') == 'E' ? 'selected' : ''}} value="E">Executado</option>
                            <option {{old('status') == 'P' ? 'selected' : ''}} value="P">Pago</option>
                            <option {{old('status') == 'C' ? 'selected' : ''}} value="C">Cancelado</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @textarea(['name' => 'comments', 'label' => 'Observações', 'required' => false])

                @checkbox(['name' => 'applyComission', 'label' => 'Aplica comissão', 'checked' => true, 'value' => old('applyComission'), 'help' => 'Se essa opção não estiver marcada o agendamento não lancará comissão ao funcionário.'])
                @checkbox(['name' => 'checkAvailability', 'label' => 'Valida disponibilidade de horário', 'value' => old('applyComission'), 'checked' => true, 'help' => 'Se desligada, o sistema não validará se o horário selecionado está disponível para o funcionário.'])
                @checkbox(['name' => 'sendEmail', 'label' => 'Envia emails de notificação', 'value' => old('applyComission'), 'checked' => true])

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <a href="{{route('booking.index')}}" class="btn btn-outline-primary me-2">Voltar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
