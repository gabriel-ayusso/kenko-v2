@extends('layouts.app')

@section('content')

<div class="row mb-5">
    <div class="col-md-6">
        <h1>Categorias de Serviços</h1>
    </div>
    <div class="col-md-6 clearfix">
        <a href="{{route('categories.create')}}" class="btn btn-primary float-right ml-2">Novo</a>
    </div>
</div>

<table class="table">
    <tr>
        <th>Nome</th>
        <th>Ordem</th>
        <th></th>
    </tr>
    @foreach($categories as $category)
    <tr>
        <td>{{$category->name}}</td>
        <td>{{$category->order}}</td>
        <td class="text-right">
            <form method="POST" action="{{route('categories.destroy', $category)}}">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="javascript:return confirm('Deseja realmente excluir esse registro? Essa ação não tem volta.')" class="btn btn-sm btn-danger">excluir</button>
                <a href="{{route('categories.edit', $category)}}" class="btn btn-sm btn-primary">editar</a>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection