@extends('layouts.app')

@section('content')
<h1>Editar Funcionário</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Por favor, preencha os campos abaixo</div>
            <div class="card-body">
                <form method="POST" action="{{route('employees.update', $employee)}}">
                    @csrf
                    @method('PUT')

                    @hidden(['name' => 'id', 'value' => old('id',$employee->id)])
                    @textbox(['name' => 'firstname', 'label' => 'Nome', 'required' => true, 'value' => old('firstname',$employee->firstname)])
                    @textbox(['name' => 'lastname', 'label' => 'Sobrenome', 'required' => true, 'value' => old('lastname',$employee->lastname)])
                    @textbox(['name' => 'title', 'label' => 'Título', 'required' => true, 'value' => old('title', $employee->title)])
                    @textarea(['name' => 'description', 'label' => 'Descrição', 'required' => true, 'value' => old('description',$employee->description)])

                    <div style="max-height: 200px; overflow-y: auto; overflow-x: hidden">
                    @foreach($services as $service)
                        @checkbox(['name' => 'services[]', 'label' => ($service->category->name . ' | ' . $service->name), 'value' => $service->id, 'checked' => $employee->services->contains($service)])
                    @endforeach
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary mr-2">Salvar</button>
                            @if(Auth::user()->admin)
                            <a href="{{route('availabilities.index', $employee)}}" class="btn btn-outline-primary mr-2">Disponibilidade</a>
                            <a href="{{route('unavailabilities.index', $employee)}}" class="btn btn-outline-warning">Ausências</a>
                            <a href="{{route('employees.special-conditions', $employee)}}" class="btn btn-outline-warning">Condições especiais</a>
                            @endif
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
                <form method="POST" action="{{route('employees.avatar', $employee)}}" enctype="multipart/form-data">
                    @csrf

                    <img src="{{route('employees.avatar', $employee)}}" style="max-height: 150px" class="img-thumbnail mb-2" alt="">

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