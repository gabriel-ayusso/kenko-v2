@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-6">
        <h1>Serviços</h1>
    </div>
    <div class="col-md-6 text-right"><a class="btn btn-primary" href="{{route('services.create')}}">Novo</a></div>
</div>

<table class="table">
    <tr>
        <th>Id</th>
        <th>Nome</th>
        <th>Categoria</th>
        <th>Comissão</th>
        <th>Tempo</th>
        <th>Preço</th>
        <th></th>
    </tr>
    @foreach($services as $service)
    <tr>
        <td>{{$service->id}}</td>
        <td><a href="{{route('services.show', $service)}}">{{$service->name}}</a>
            @if($service->private) <i class="fas fa-lock text-danger"></i> @endif
        </td>
        <td>{{$service->category->name}}</td>
        <td>{{number_format($service->comission)}}%</td>
        <td>{{$service->time}} min</td>
        <td>R$ {{number_format($service->price, 2)}}</td>
        <td>
            <a href="{{route('services.edit', $service)}}" class="btn btn-sm btn-primary">editar</a>
        </td>
    </tr>
    @endforeach
</table>

@endsection