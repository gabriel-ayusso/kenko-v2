@extends('layouts.app')

@php

function availabilityExists($service, $weekday, $time)
{
foreach($service->availabilities as $item) {
if($item->weekday == $weekday && $item->time == $time)
return true;
}
return false;
}

@endphp

@section('content')
<h1>Editar serviço</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Por favor, preencha os campos abaixo</div>
            <div class="card-body">
                <form method="POST" action="{{route('services.update', $service)}}">
                    @csrf
                    @method('PUT')

                    @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'value' => $service->name])
                    @textbox(['name' => 'description', 'label' => 'Descrição', 'required' => true, 'value' => $service->description])
                    @textbox(['name' => 'time', 'label' => 'Duração (min)', 'required' => true, 'help' => 'Tempo em minutos que o serviço leva', 'value' => $service->time])
                    @textbox(['name' => 'price', 'label' => 'Preço (R$)', 'required' => true, 'value' => $service->price])
                    @textbox(['name' => 'comission', 'label' => 'Comissão (%)', 'required' => true, 'value' => $service->comission])
                    <div class="form-group row">
                        <label for="weekday" class="col-sm-2 col-form-label">Categoria</label>
                        <div class="col-md-10">
                            <select class="form-select" name="category_id" id="category_id">
                                <option value="">--selecione--</option>
                                @foreach($categories as $category)
                                <option value="{{$category->id}}" {{$category->id === $service->category_id ? 'selected' : ''}}>{{$category->name}}</option>
                                @endforeach
                            </select>
                            @error('weekday')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="caService" class="col-sm-2 col-form-label">Conta Azul</label>
                        <div class="col-md-10">
                            <select class="form-select" name="ca_id" id="ca_id">
                                <option value="">--selecione--</option>
                                @foreach($caServices as $caService)
                                <option value="{{$caService->id}}" {{$caService->id === $service->ca_id ? 'selected' : ''}}>{{$caService->name}}</option>
                                @endforeach
                            </select>
                            @error('caService')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @checkbox(['name' => 'private', 'label' => 'Privado', 'checked' => $service->private])

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <a href="{{route('services.index')}}" class="btn btn-outline-primary mr-2">Voltar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Foto</div>
            <div class="card-body">
                <form method="POST" action="{{route('services.avatar', $service)}}" enctype="multipart/form-data">
                    @csrf

                    <img src="{{route('services.avatar', $service)}}" style="max-height: 150px" class="img-thumbnail mb-2" alt="">

                    <div class="custom-file">
                        <input type="file" name="avatar" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Selecionar...</label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
