@extends('layouts.app')

@section('content')
<h1>{{$service->name}}</h1>

<input type="hidden" id="serviceId" value="{{$service->id}}">

<form method="POST" action="{{route('booking.guestBookingConfirm', ['service' => $service])}}">
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

    @hidden(['name' => 'service_id', 'value' => $service->id])
    @hidden(['name' => 'employee_id', 'value' => ''])

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Selecione o dia e horários</h4>
                </div>
                <div class="card-body">
                    @textbox(['name' => 'date', 'type' => 'date', 'label' => 'Data', 'required' => true, 'help' => 'Selecione o melhor dia para você.'])
                    <div id="service-feedback"></div>
                    <div id="horarios"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Informe seus dados</h4>
                </div>
                <div class="card-body">
                    @textbox(['name' => 'name', 'label' => 'Nome', 'required' => true, 'help' => 'Digite o seu nome completo'])
                    @textbox(['name' => 'email', 'type' => 'email', 'label' => 'Email'])
                    @textbox(['name' => 'phone', 'label' => 'Telefone', 'type' => 'number', 'help' => 'Insira apenas números com DDD. Exemplo: 11999999999'])

                    <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Agendar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</form>

@endsection

@section('scripts')
<script src="{{ asset('js/guest-booking.js') }}" defer></script>
@endsection
