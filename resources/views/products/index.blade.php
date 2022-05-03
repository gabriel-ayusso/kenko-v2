@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1>Produtos</h1>
    </div>
    <div class="col-md-6 text-end"><a class="btn btn-primary" href="{{route('products.create')}}">Novo</a></div>
</div>

<table class="table">
    <tr>
        <th>Nome</th>
        <th>Descrição</th>
        <th>Comissão</th>
        <th>Preço</th>
        <th></th>
    </tr>
    @foreach($products as $product)
    <tr>
        <td>{{$product->name}}</td>
        <td>{{$product->description}}</td>
        <td>{{number_format($product->comission)}}%</td>
        <td>R$ {{number_format($product->price, 2)}}</td>
        <td class="text-end">
            <form method="POST" action="{{route('products.destroy', $product)}}">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Deseja realmente excluir esse produto?')" class="btn btn-sm btn-danger ml-2">excluir</button>
                <a href="{{route('products.edit', $product)}}" class="btn btn-sm btn-primary ml-2">editar</a>
            </form>
        </td>
    </tr>
    @endforeach
</table>

@endsection
