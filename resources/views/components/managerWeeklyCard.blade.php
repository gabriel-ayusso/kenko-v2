@php
$bookingDate = DateTime::createFromFormat('Y-m-d H:i:s', $item->bookingDate);
@endphp

<span title="{{$item->serviceName}}" class="appointment badge bg-info {{status($item->bookingStatus)}}">
    <div href="/manager/booking/{{$item->bookingId}}/edit" style="position: relative;">
        <span>{{$bookingDate->format('H:i')}}</span> <span>{{$item->customerName}}</span><br>
        <small>R$ {{$item->servicePrice}}</small><br>
        <div class="mt-2">
            <a href="http://127.0.0.1:8000/manager/booking/{{$item->bookingId}}/edit"><i class="fas fa-search fa-lg"></i></a>
            <a href="http://127.0.0.1:8000/manager/{{$item->bookingId}}/pay" onclick="return confirm('Deseja marcar esse agendamento como pago?');" alt="Marcar como pago" style="position:absolute; right:0; bottom: 0;"><i class="fas fa-money-bill fa-2x"></i></a>
        </div>
    </div>
</span>
