@extends('layouts.app')

@section('content')
    <h1 class="text-success">Confirmação concluída.</h1>

    <p>Obrigado por confirmar seu agendamento.</p>

    <p>Não se esqueça de marcar na sua agenda, no dia {{$booking->date->format('d/m/Y')}} às {{$booking->date->format('H:i')}} horas.</p>

    <p>Obrigado!</p>
@endsection