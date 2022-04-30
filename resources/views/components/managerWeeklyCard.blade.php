@php
$bookingDate = DateTime::createFromFormat('Y-m-d H:i:s', $item->bookingDate);
@endphp

@if($booking->status != 'C')
<span title="{{$item->serviceName}}" class="appointment badge {{status($item->bookingStatus)}}">
    <div href="/manager/booking/{{$item->bookingId}}/edit" style="position: relative;">
        <span>{{$bookingDate->format('H:i')}}</span> <span>{{$item->customerName}}</span><br>
        <small>R$ {{$item->servicePrice}}</small><br>
        <div class="mt-2">
            <a href="{{route('booking.edit', $booking)}}"><i class="fas fa-search fa-lg"></i></a>
            @if($booking->status === 'A')
            <a href="{{route('manager.pay', $booking)}}" onclick="return confirm('Deseja marcar esse agendamento como pago?');" alt="Marcar como pago" style="position:absolute; right:0; bottom: 0;"><i class="fas fa-money-bill fa-2x"></i></a>
            @endif
        </div>
    </div>
</span>
@endif
