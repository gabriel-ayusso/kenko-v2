@component('mail::message')
# Agendamento confirmado

[logo]: {{asset('img/logo-verde.png')}} "Logo"

<p>Olá <strong>{{$booking->name}}</strong>!</p>

<p>Seu agendamento de {{$booking->service->name}} em {{$booking->date->format('d/m/Y')}} às <strong>{{$booking->date->format('H:i')}}</strong> horas está confirmado!</p>

<p>O profissional <strong>{{$booking->employee->firstname}}</strong> estará aguardando você no horário marcado.</p>

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
