@extends('layouts.app')

@section('content')
<h1>Novo Funcionário</h1>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('employees.store')}}">
                @csrf

                @textbox(['name' => 'firstname', 'label' => 'Nome', 'required' => true])
                @textbox(['name' => 'lastname', 'label' => 'Sobrenome', 'required' => true])
                @textbox(['name' => 'title', 'label' => 'Título', 'required' => true])
                @textarea(['name' => 'description', 'label' => 'Descrição', 'required' => true])

                <div style="max-height: 200px; overflow-y: auto; overflow-x: hidden">
                @foreach($services as $service)
                    @checkbox(['name' => 'services[]', 'label' => ($service->category->name . ' | ' . $service->name), 'value' => $service->id])
                @endforeach
                </div>

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection