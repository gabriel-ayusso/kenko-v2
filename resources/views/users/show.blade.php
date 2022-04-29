@extends('layouts.app')

@section('content')

    <h1>{{$user->name}} <small class="text-muted">Usuário</small></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Dados do Usuário</div>
                <div class="card-body">
                    <p>Email: {{$user->email}}</p>
                    <p>Data de Criação: {{$user->created_at}}</p>
                    <p>Data de Alteração: {{$user->updated_at}}</p>

                    <form method="POST" action="{{route('users.update', $user)}}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <select name="employee_id" class="form-control">
                                <option value="0">selecione um funcionário</option>
                                @foreach($employees as $employee)
                                    <option value="{{$employee->id}}" {{ $user->employee_id == $employee->id ? 'selected' : '' }}>{{$employee->firstname}} {{$employee->lastname}}</option> 
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mb-2">Salvar</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection