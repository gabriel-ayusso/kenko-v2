@extends('layouts.app')

@section('content')
<h1>Nova disponibilidade para {{$employee->firstname}}</h1>

<div class="card">
    <div class="card-header">Por favor preencha os campos abaixo</div>

    <div class="card-body">
        <form method="POST" action="{{route('availabilities.store', $employee)}}">
            @csrf

            <div class="form-group row">
                <label for="weekday" class="col-sm-2 col-form-label">Dia da Semana</label>
                <div class="col-md-10">
                    <select class="form-control" name="weekday" id="weekday">
                        <option value="-1">--selecione--</option>
                        <option value="7">Domingo</option>
                        <option value="1">Segunda</option>
                        <option value="2">Terça</option>
                        <option value="3">Quarta</option>
                        <option value="4">Quinta</option>
                        <option value="5">Sexta</option>
                        <option value="6">Sábado</option>
                    </select>
                    @error('weekday')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @textbox(['name' => 'start', 'label' => 'Início', 'type' => 'time'])
            @textbox(['name' => 'end', 'label' => 'Fim', 'type' => 'time'])

            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection