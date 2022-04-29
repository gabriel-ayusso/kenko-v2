@extends('layouts.app')

@section('content')
    <h1>{{$service->name}}</h1>
    <p>
        {{$service->description}}
    </p>

    <div class="row mb-3">
        <div class="col-md-3">
            <h5><i class="far fa-clock"></i> {{$service->time}} minutos</h5>
        </div>
        <div class="col-md-3">
            <h5><i class="far fa-money-bill-alt"></i> R$ {{number_format($service->price, 2)}}</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{route('services.destroy', $service)}}" class="form-inline">
                @csrf
                @method('DELETE')
                <a href="{{route('services.index')}}" class="btn btn-outline-primary mr-2">Voltar</a>
                <a href="{{route('services.edit', $service)}}" class="btn btn-primary mr-2">Editar</a>
                <button type="submit" onclick="return confirm('Você confirma a exclusão desse serviço?')" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>
@endsection