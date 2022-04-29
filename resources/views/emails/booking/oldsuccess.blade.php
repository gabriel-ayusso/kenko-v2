@component('mail::message')
# Agendamento confirmado

@component('mail::panel')
    {{$booking->service->name}} - {{$booking->date->format('d/m/Y H:i')}}
@endcomponent

<p>Olá <strong>{{$booking->name}}</strong>!</p>

<p>Seu agendamento de {{$booking->service->name}} em {{$booking->date->format('d/m/Y')}} às <strong>{{$booking->date->format('H:i')}} horas foi recebido!</p>

<p>Para confirmar seu agendamento, clique no botão abaixo.</p>

@component('mail::button', ['url' => route('bookings.confirm', ['booking' => $booking, 'token' => $booking->confirmation_hash])])
Confirmar
@endcomponent


<p><strong>{{$booking->employee->firstname}}</strong> {{$booking->employee->lastname}} estará aguardando.</p>

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
