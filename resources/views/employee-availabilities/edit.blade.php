@extends('layouts.app')

@section('content')
<h1>Nova disponibilidade para {{$employee->firstname}}</h1>

<div class="card">
    <div class="card-header">Por favor preencha os campos abaixo</div>

    <div class="card-body">
        <form method="POST" action="{{route('availabilities.update', ['employee' => $employee, 'availability' => $availability])}}">
            @csrf
            @method('PUT')

            <div class="form-group row">
                <label for="weekday" id="weekday" class="col-sm-2 col-form-label">Dia da Semana</label>
                <div class="col-md-10">
                    <select class="form-control" name="weekday" id="weekday">
                        <option value="-1">--selecione--</option>
                        <option @if(old('weekday', $availability->weekday) == 7) selected @endif value="7">Domingo</option>
                        <option @if(old('weekday', $availability->weekday) == 1) selected @endif value="1">Segunda</option>
                        <option @if(old('weekday', $availability->weekday) == 2) selected @endif value="2">Terça</option>
                        <option @if(old('weekday', $availability->weekday) == 3) selected @endif value="3">Quarta</option>
                        <option @if(old('weekday', $availability->weekday) == 4) selected @endif value="4">Quinta</option>
                        <option @if(old('weekday', $availability->weekday) == 5) selected @endif value="5">Sexta</option>
                        <option @if(old('weekday', $availability->weekday) == 6) selected @endif value="6">Sábado</option>
                    </select>
                    @error('weekday')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @textbox(['name' => 'start', 'label' => 'Início', 'type' => 'time', 'value' => old('start', $availability->start->format('H:i')) ])
            @textbox(['name' => 'end', 'label' => 'Fim', 'type' => 'time', 'value' => old('end', $availability->end->format('H:i'))])

            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <a href="{{route('availabilities.index', $employee)}}" class="btn btn-outline-primary">Voltar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection