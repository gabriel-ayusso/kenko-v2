@extends('layouts.app')

@section('content')
<h1>Alterar Categoria de Serviço</h1>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('categories.update', $category)}}">
                @csrf
                @method('PUT')

                @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'value' => old('name', $category->name)])
                @textbox(['name' => 'order', 'label' => 'Ordem', 'required' => true, 'type' => 'number', 'help' => 'Ordem que em essa categoria aparece na tela de agendamento', 'value' => old('order', $category->order)])

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <a href="{{route('categories.index')}}" class="btn btn-outline-primary ml-2">Voltar</a>
                        <button type="submit" class="btn btn-primary ml-2">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection