@extends('layouts.app')

@section('content')
<h1>Editar Usuário</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Por favor, preencha os campos abaixo</div>
            <div class="card-body">
                <form method="POST" action="{{route('users.update', $user)}}">
                    @csrf
                    @method('PUT')

                    @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'value' => old('name',$user->name)])
                    @textbox(['name' => 'email', 'type' => 'email', 'label' => 'E-mail', 'required' => true, 'value' => old('email', $user->email)])
                    @checkbox(['name' => 'admin', 'label' => 'Admin', 'checked' => old('admin',$user->admin)])
                    @checkbox(['name' => 'manager', 'label' => 'Gerente', 'checked' => old('manager',$user->manager)])
                    @checkbox(['name' => 'active', 'label' => 'Ativo', 'checked' => old('active',$user->active)])
                    @checkbox(['name' => 'agenda', 'label' => 'Agenda', 'checked' => old('agenda',$user->agenda)])

                    <div class="form-group row">
                        <label for="employee_id" class="col-sm-2 col-form-label">Funcionário</label>
                        <div class="col-md-10">
                            <select name="employee_id" class="form-control">
                                <option value="">selecione um funcionário</option>
                                @foreach($employees as $employee)
                                <option value="{{$employee->id}}" {{ $user->employee_id == old('employee_id',$employee->id) ? 'selected' : '' }}>{{$employee->firstname}} {{$employee->lastname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <a href="javascript:history.back()" class="btn btn-outline-primary">Voltar</a>
                            <button type="submit" class="btn btn-primary mr-4">Salvar</button>
                            <button type="submit" form="reset-password" class="btn btn-warning">Alterar a senha</button>
                        </div>
                    </div>
                </form>

                <form id="reset-password" method="POST" action="{{route('users.password-reset', $user)}}">
                    @csrf
                    @method('PATCH')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection