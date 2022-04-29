@extends('layouts.app')

@section('content')

<div class="alert alert-success">
    <h5>Obrigado {{$booking->name}}!</h5>

    <p>Seu agendamento de {{$booking->service->name}} às {{$booking->date->format('H:i')}} do dia {{$booking->date->format('d/m/Y')}} foi realizado com sucesso.</p>

    <p>O profissional {{$booking->employee->firstname}} {{$booking->employee->lastname}} estará aguardando.</p>

    <p>KENKO</p>

</div>

<!-- Event snippet for Agendamento Online conversion page -->
<script>
  gtag('event', 'conversion', {'send_to': 'AW-721404582/wkgrCJ-XnNEBEKaF_9cC'});
</script>

@endsection