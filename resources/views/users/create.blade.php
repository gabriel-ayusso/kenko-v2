@extends('layouts.app')

@section('content')
<h1>Novo Usuário</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Por favor, preencha os campos abaixo</div>
            <div class="card-body">
                <form method="POST" action="{{route('users.store')}}">
                    @csrf

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'value' => old('name')])
                    @textbox(['name' => 'email', 'type' => 'email', 'label' => 'E-mail', 'required' => true, 'value' => old('email')])
                    @checkbox(['name' => 'admin', 'label' => 'Admin', 'checked' => old('admin')])
                    @checkbox(['name' => 'manager', 'label' => 'Gerente', 'checked' => old('manager')])
                    @checkbox(['name' => 'active', 'label' => 'Ativo', 'checked' => old('active')])
                    @checkbox(['name' => 'agenda', 'label' => 'Agenda', 'checked' => old('agenda')])

                    <div class="form-group row">
                        <label for="employee_id" class="col-sm-2 col-form-label">Funcionário</label>
                        <div class="col-md-10">
                            <select name="employee_id" class="form-control">
                                <option value="">selecione um funcionário</option>
                                @foreach($employees as $employee)
                                <option value="{{$employee->id}}" {{ $employee->id == old('employee_id') ? 'selected' : '' }}>{{$employee->firstname}} {{$employee->lastname}}</option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-10 offset-sm-2">
                            <a href="javascript:history.back()" class="btn btn-outline-primary">Voltar</a>
                            <button type="submit" class="btn btn-primary mr-4">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection