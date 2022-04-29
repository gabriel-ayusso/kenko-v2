@extends('layouts.app')

@section('content')
<h1>Novo serviço</h1>

<div class="col-md-8">
    <div class="card">
        <div class="card-header">Por favor, preencha os campos abaixo</div>
        <div class="card-body">
            <form method="POST" action="{{route('services.store')}}">
                @csrf

                @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true])
                @textbox(['name' => 'description', 'label' => 'Descrição', 'required' => true])
                @textbox(['name' => 'time', 'label' => 'Duração (min)', 'required' => true, 'help' => 'Tempo em minutos que o serviço leva'])
                @textbox(['name' => 'price', 'label' => 'Preço (R$)', 'required' => true])
                @textbox(['name' => 'comission', 'label' => 'Comissão (%)', 'required' => true])
                <div class="form-group row">
                    <label for="weekday" class="col-sm-2 col-form-label">Categoria</label>
                    <div class="col-md-10">
                        <select class="form-control" name="category_id" id="category_id">
                            <option value="">--selecione--</option>
                            @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                        @error('weekday')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @checkbox(['name' => 'private', 'label' => 'Privado'])

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
@endsection