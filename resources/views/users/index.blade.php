@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h1>Usuários do sistema</h1>
    </div>
    <div class="col-md-6 clearfix">
        <a href="{{route('users.create')}}" class="btn btn-primary float-right"><i class="fa fa-plus me-2"></i> Novo</a>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Funcionário</th>
            <th>Dt. Criação</th>
            <th>Último login</th>
            <th></th>
        </tr>
        @foreach($users as $user)
        <tr>
            <td>{{$user->id}}</td>
            <td>
                {{$user->name}}
                @if($user->id == Auth::user()->id) <span class="badge bg-info">é você!</span> @endif
                @if($user->admin) <span class="badge bg-warning">admin</span> @endif
                @if($user->manager) <span class="badge bg-secondary">gerente</span> @endif
                @if(!$user->active) <span class="badge bg-danger">inativo</span> @endif
            </td>
            <td>{{$user->email}}</td>
            <td>{{$user->employee_id > 0 ? $user->employee->firstname . ' ' . $user->employee->lastname : '-'}}</td>
            <td>{{$user->created_at->format('d/m/Y H:i:s')}}</td>
            <td>{{$user->last_login ? $user->last_login->format('d/m/Y H:i:s') : '-'}}</td>
            <td>
                <a href="{{route('users.edit', $user)}}" class="btn btn-sm btn-primary">editar</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
