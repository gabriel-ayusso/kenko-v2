@extends('layouts.app')

@section('content')
<h1>Novo produto</h1>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('products.update', $product)}}">
                @csrf
                @method('PUT')

                @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'value' => old('name', $product->name)])
                @textbox(['name' => 'description', 'label' => 'Descrição', 'required' => true, 'value' => old('name', $product->description)])
                @textbox(['name' => 'price', 'label' => 'Preço (R$)', 'required' => true, 'value' => old('name', $product->price)])
                @textbox(['name' => 'comission', 'label' => 'Comissão (%)', 'required' => true, 'value' => old('name', $product->comission)])

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <a href="{{route('products.index')}}" class="btn btn-outline-primary mr-2">Voltar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection